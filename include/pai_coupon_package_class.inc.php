<?php
/**
 * �Ż�ȯ�ײ�
 * @author Henry
 * @copyright 2015-05-09
 */

class pai_coupon_package_class extends POCO_TDG
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_coupon_db');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_package_tbl()
	{
		$this->setTableName('coupon_package_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_package_ref_batch_tbl()
	{
		$this->setTableName('coupon_package_ref_batch_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_package_cate_tbl()
	{
		$this->setTableName('coupon_package_cate_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_exchange_tbl()
	{
		$this->setTableName('coupon_exchange_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_exchange_ref_coupon_tbl()
	{
		$this->setTableName('coupon_exchange_ref_coupon_tbl');
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_package($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_package_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public function update_package($data, $package_id)
	{
		$package_id = intval($package_id);
		if( !is_array($data) || empty($data) || $package_id<1 )
		{
			return false;
		}
		$this->set_coupon_package_tbl();
		$affected_rows = $this->update($data, "package_id={$package_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �ۻ�ʵ����ʹ����
	 * @param string $where_str
	 * @param int $number
	 * @return boolean
	 */
	private function margin_package_real_number($package_id, $number=1)
	{
		$package_id = intval($package_id);
		$number = intval($number);
		if( $package_id<1 || $number<1 )
		{
			return false;
		}
		$this->set_coupon_package_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_number=real_number+{$number} WHERE package_id={$package_id} AND (plan_number-real_number)>={$number}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param string $package_sn
	 * @return array
	 */
	public function get_package_info($package_sn)
	{
		$package_sn = trim($package_sn);
		if( strlen($package_sn)<1 )
		{
			return array();
		}
		$where_str = 'package_sn=:x_package_sn';
		sqlSetParam($where_str, 'x_package_sn', $package_sn);
		$this->set_coupon_package_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $package_id
	 * @return array
	 */
	public function get_package_info_by_id($package_id)
	{
		$package_id = intval($package_id);
		if( $package_id<1 )
		{
			return array();
		}
		$this->set_coupon_package_tbl();
		return $this->find("package_id={$package_id}");
	}

	/**
	 * ��ȡ��Ϣ
	 * @param string $coupon_sn
	 * @return array
	 */
	public function get_package_info_by_coupon_sn($coupon_sn)
	{
        $coupon_sn = trim($coupon_sn);
        if( strlen($coupon_sn)<1 )
        {
            return array();
        }
		$where_str = 'coupon_sn=:x_coupon_sn';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);

        $this->set_coupon_exchange_ref_coupon_tbl();
        $package_sn_info = $this->find($where_str, null, 'package_sn');
        if( empty($package_sn_info) )
        {
            return array();
        }
        $package_sn = trim($package_sn_info['package_sn']);
        return $this->get_package_info($package_sn);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $cate_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_package_list($cate_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$cate_id = intval($cate_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $cate_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "cate_id={$cate_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_coupon_package_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return bool
	 */
	private function add_ref_batch($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return false;
		}
		$this->set_coupon_package_ref_batch_tbl();
		$this->insert($data);
		return true;
	}
	
	/**
	 * ����
	 * @param int $package_id
	 * @param int $batch_id
	 * @param int $quantity
	 * @param int $coupon_days
	 * @return boolean
	 */
	public function save_ref_batch($package_id, $batch_id, $quantity, $coupon_days)
	{
		$package_id = intval($package_id);
		$batch_id = intval($batch_id);
		$quantity = intval($quantity);
		$coupon_days = intval($coupon_days);
		if( $package_id<1 || $batch_id<1 || $quantity<1 || $coupon_days<0 )
		{
			return false;
		}
		$data = array(
			'package_id' => $package_id,
			'batch_id' => $batch_id,
			'quantity' => $quantity,
			'coupon_days' => $coupon_days,
		);
		$this->set_coupon_package_ref_batch_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}
	
	/**
	 * ɾ��
	 * @param int $package_id
	 * @param int $batch_id
	 * @return boolean
	 */
	public function del_ref_batch($package_id, $batch_id=0)
	{
		$package_id = intval($package_id);
		$batch_id = intval($batch_id);
		if( $package_id<1)
		{
			return false;
		}
		$where_str = "package_id='{$package_id}'";
		if( $batch_id>0 )
		{
			$where_str .= " AND batch_id='{$batch_id}'";
		}
		$this->set_coupon_package_ref_batch_tbl();
		$this->delete($where_str);
		return true;
	}
	
	/**
	 * ��ȡ
	 * @param int $package_id
	 * @param int $batch_id
	 * @return array
	 */
	public function get_ref_batch_info($package_id, $batch_id)
	{
		$package_id = intval($package_id);
		$batch_id = intval($batch_id);
		if( $package_id<1 || $batch_id<1 )
		{
			return array();
		}
		$where_str = "package_id='{$package_id}' AND batch_id='{$batch_id}'";
		$this->set_coupon_package_ref_batch_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $package_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_ref_batch_list($package_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$package_id = intval($package_id);
		if( $package_id<1 )
		{
			return $b_select_count ? 0 : array();
		}
		
		//�����ѯ����
		$sql_where = "package_id={$package_id}";
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_coupon_package_ref_batch_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_cate($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_package_cate_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $cate_id
	 * @return boolean
	 */
	public function update_cate($data, $cate_id)
	{
		//������
		$cate_id = intval($cate_id);
		if( !is_array($data) || empty($data) || $cate_id<1 )
		{
			return false;
		}
		//����
		$this->set_coupon_package_cate_tbl();
		$affected_rows = $this->update($data, "cate_id={$cate_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $cate_id
	 * @return array
	 */
	public function get_cate_info($cate_id)
	{
		$cate_id = intval($cate_id);
		if( $cate_id<1 )
		{
			return array();
		}
		$this->set_coupon_package_cate_tbl();
		return $this->find("cate_id={$cate_id}");
	}
	
	/**
	 * ��ȡ�����б�
	 * @param int $parent_id -1��ʾ������
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_cate_list($parent_id=-1, $b_select_count=false, $where_str='', $order_by='sort ASC,cate_id ASC', $limit='0,20', $fields='*')
	{
		//������
		$parent_id = intval($parent_id);
		
		//�����ѯ����
		$sql_where = '';
		if( $parent_id>=0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "parent_id={$parent_id}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_coupon_package_cate_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ��ȡ���з���
	 * @param int $parent_id
	 * @param int $exclude_cate_id
	 * @return array
	 */
	public function get_cate_list_all($parent_id=0, $exclude_cate_id=0)
	{
		//������
		$parent_id = intval($parent_id);
		$exclude_cate_id = intval($exclude_cate_id);
		if( $parent_id<0 )
		{
			return array();
		}
		
		//��ȡ����
		$ret_list = array();
		$cate_list = $this->get_cate_list($parent_id, false, "cate_id<>{$exclude_cate_id}", 'sort ASC,cate_id ASC', '0,99999999');
		foreach($cate_list as $cate_info)
		{
			$cate_id = intval($cate_info['cate_id']);
			$child_list = $this->get_cate_list_all($cate_id, $exclude_cate_id);
			$ret_list[] = $cate_info;
			if( !empty($child_list) )
			{
				$ret_list = array_merge($ret_list, $child_list);
			}
		}
		return $ret_list;
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_exchange($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_exchange_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $user_id
	 * @param string $package_sn
	 * @return array
	 */
	public function get_exchange_info($user_id, $package_sn)
	{
		$user_id = intval($user_id);
		$package_sn = trim($package_sn);
		if( $user_id<1 || strlen($package_sn)<1 )
		{
			return array();
		}
		$where_str = "user_id={$user_id} AND package_sn=:x_package_sn";
		sqlSetParam($where_str, 'x_package_sn', $package_sn);
		$this->set_coupon_exchange_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $user_id
	 * @param int $cate_id
	 * @return array
	 */
	public function get_exchange_info_by_cate_id($user_id, $cate_id)
	{
		$user_id = intval($user_id);
		$cate_id = intval($cate_id);
		if( $user_id<1 || $cate_id<1 )
		{
			return array();
		}
		$where_str = "user_id={$user_id} AND cate_id={$cate_id}";
		$this->set_coupon_exchange_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_ref_coupon($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_exchange_ref_coupon_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ��ȡ����ַ���
	 * @param int $length
	 * @return string
	 */
	private function get_rand_str($length)
	{
		$code = '';
		$length = intval($length);
		if( $length<1 )
		{
			return $code;
		}
		//��һλ�涨��9
		$code = '9';
		$length--;
		$pattern = '2935714068';
		for($i=0; $i<$length; $i++)
		{
			$code .= substr($pattern, mt_rand(0,9), 1);
		}
		return $code;
	}
	
	/**
	 * �����ײ�ȯ
	 * @param int $cate_id ����ID
	 * @param int $start_time
	 * @param int $end_time
	 * @param int $plan_number
	 * @param array $batch_list array('batch_id'=>0, 'quantity'=>0)
	 * @param array $more_info array('scope_user_divide'=>'', 'package_title'=>'', 'package_remark'=>'')
	 * @return array array('result'=>0, 'message'=>'', 'package_sn'=>'')
	 */
	public function create_package($cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'package_sn'=>'');
		
		//������
		$cate_id = intval($cate_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);
		$plan_number = intval($plan_number);
		if( !is_array($batch_list) ) $batch_list = array();
		if( $cate_id<1 || $start_time<1 || $end_time<1 || $start_time>$end_time || $plan_number<1 || empty($batch_list) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$scope_user_divide = trim($more_info['scope_user_divide']);
		$package_title = trim($more_info['package_title']);
		$package_remark = trim($more_info['package_remark']);
		
		//�����������
		$batch_data_list = array();
		foreach($batch_list as $batch_info)
		{
			$batch_id_tmp = intval($batch_info['batch_id']);
			$quantity_tmp = intval($batch_info['quantity']);
			if( $batch_id_tmp<1 || $quantity_tmp<1 )
			{
				$result['result'] = -2;
				$result['message'] = '�������δ���';
				return $result;
			}
			$batch_data_list[] = array(
				'batch_id' => $batch_id_tmp,
				'quantity' => $quantity_tmp,
			);
		}
		
		$package_id = 0;
		$package_sn = '';
		$while_count = 0;
		while($while_count<9999)
		{
			$rand_str = $this->get_rand_str(6);
			if( strlen($rand_str)<1 )
			{
				//�ײ�ȯ��Ϊ��
				break;
			}
			$tmp = $this->get_package_info($rand_str);
			if( !empty($tmp) )
			{
				//�ײ�ȯ���Ѵ���
				$while_count++;
				continue;
			}
			$package_data = array(
				'package_sn' => $rand_str,
				'cate_id' => $cate_id,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'plan_number' => $plan_number,
				'scope_user_divide' => $scope_user_divide,
				'package_title' => $package_title,
				'package_remark' => $package_remark,
				'add_time' => time(),
			);
			$id = $this->add_package($package_data);
			if( $id<1 )
			{
				//��������һ������Ҳ�������ײ�ȯ
				$while_count++;
				continue;
			}
			$package_id = $id;
			$package_sn = $rand_str;
			break;
		}
		if( $package_id<1 || strlen($package_sn)<1 )
		{
			$result['result'] = -3;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//�����������
		foreach($batch_data_list as $batch_data_info)
		{
			$batch_data_info['package_id'] = $package_id;
			$this->add_ref_batch($batch_data_info);
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['package_sn'] = $package_sn;
		return $result;
	}
	
	/**
	 * ���ɶ���ײ�ȯ 
	 * @param int $cate_id ����ID
	 * @param int $start_time
	 * @param int $end_time
	 * @param int $plan_number
	 * @param array $batch_list array('batch_id'=>0, 'quantity'=>0)
	 * @param int $quantity
	 * @param array $more_info array('scope_user_divide'=>'', 'package_title'=>'', 'package_remark'=>'')
	 * @return array array('result'=>0, 'message'=>'', 'quantity'=>0)
	 */
	public function create_package_multiple($cate_id, $start_time, $end_time, $plan_number, $batch_list, $quantity, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'quantity'=>0);
		
		//������
		$cate_id = intval($cate_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);
		$plan_number = intval($plan_number);
		if( !is_array($batch_list) ) $batch_list = array();
		$quantity = intval($quantity);
		if( $cate_id<1 || $start_time<1 || $end_time<1 || $start_time>$end_time || $plan_number<1 || empty($batch_list) || $quantity<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		
		//�����Ż�ȯ
		$create_quantity = 0; //ʵ����������
		for($i=1; $i<=$quantity; $i++)
		{
			$create_ret = $this->create_package($cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info);
			if( $create_ret['result']!=1 )
			{
				break;
			}
			$create_quantity++;
		}
		if( $create_quantity<1 )
		{
			$result['result'] = -2;
			$result['message'] = '����ʧ��';
			return $result;
		}
		if( $create_quantity<$quantity )
		{
			$result['result'] = 1;
			$result['message'] = '���ֳɹ�';
			$result['quantity'] = $create_quantity;
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['quantity'] = $create_quantity;
		return $result;
	}
	
	/**
	 * �ύ�һ���
	 * @param string $package_sn
	 * @param int $cate_id
	 * @param int $start_time
	 * @param int $end_time
	 * @param int $plan_number
	 * @param array $batch_list array( array('batch_id'=>0, 'quantity'=>0, 'coupon_days'=>0), ... )
	 * @param array $more_info array('scope_user_divide'=>'', 'msg_url'=>'', 'msg_content'=>'', 'package_title'=>'', 'package_remark'=>'')
	 * @return array array('result'=>0, 'message'=>'', 'package_id'=>0)
	 */
	public function submit_package($package_sn, $cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'package_id'=>0);
		
		//������
		$package_sn = trim($package_sn);
		$cate_id = intval($cate_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);
		$plan_number = intval($plan_number);
		if( !is_array($batch_list) ) $batch_list = array();
		if( strlen($package_sn)<1 || $cate_id<1 || $start_time<1 || $end_time<1 || $start_time>$end_time || $plan_number<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$scope_user_divide = trim($more_info['scope_user_divide']);
		$msg_url = trim($more_info['msg_url']);
		$msg_content = trim($more_info['msg_content']);
		$package_title = trim($more_info['package_title']);
		$package_remark = trim($more_info['package_remark']);
		
		//�����������
		$batch_data_list = array();
		foreach($batch_list as $batch_info)
		{
			$batch_id_tmp = intval($batch_info['batch_id']);
			$quantity_tmp = intval($batch_info['quantity']);
			$coupon_days_tmp = intval($batch_info['coupon_days']);
			if( $batch_id_tmp<1 || $quantity_tmp<1 )
			{
				$result['result'] = -2;
				$result['message'] = '�������δ���';
				return $result;
			}
			$batch_data_list[] = array(
				'batch_id' => $batch_id_tmp,
				'quantity' => $quantity_tmp,
				'coupon_days' => $coupon_days_tmp,
			);
		}
		
		//���һ����Ƿ��Ѵ���
		$tmp = $this->get_package_info($package_sn);
		if( !empty($tmp) )
		{
			$result['result'] = -3;
			$result['message'] = '�һ����Ѵ���';
			return $result;
		}
		
		//����
		$package_data = array(
			'package_sn' => $package_sn,
			'cate_id' => $cate_id,
			'start_time' => $start_time,
			'end_time' => $end_time,
			'plan_number' => $plan_number,
			'scope_user_divide' => $scope_user_divide,
			'msg_url' => $msg_url,
			'msg_content' => $msg_content,
			'package_title' => $package_title,
			'package_remark' => $package_remark,
			'add_time' => time(),
		);
		$package_id = $this->add_package($package_data);
		if( $package_id<1 )
		{
			$result['result'] = -4;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//�����������
		foreach($batch_data_list as $batch_data_info)
		{
			$batch_data_info['package_id'] = $package_id;
			$this->add_ref_batch($batch_data_info);
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['package_id'] = $package_id;
		return $result;
	}
	
	/**
	 * ����ײ�ȯ����Ч��
	 * @param array $coupon_info
	 * @param int $cur_time ��ǰʱ��
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function check_package_valid($package_info, $cur_time=0)
	{
		$result = array('result'=>0, 'message'=>'');
	
		if( !is_array($package_info) || empty($package_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��ȯ������';
			return $result;
		}
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
	
		$package_sn = trim($package_info['package_sn']);
		if( strlen($package_sn)<1 )
		{
			$result['result'] = -2;
			$result['message'] = '��ȯ������';
			return $result;
		}
		
		$start_time = intval($package_info['start_time']);
		if( $start_time>$cur_time )
		{
			$result['result'] = -3;
			$result['message'] = '��ȯδ����Ч��';
			return $result;
		}
		
		$end_time = intval($package_info['end_time']);
		if( $end_time<$cur_time )
		{
			$result['result'] = -4;
			$result['message'] = '��ȯ�ѹ���Ч��';
			return $result;
		}
		
		$plan_number = intval($package_info['plan_number']);
		$real_number = intval($package_info['real_number']);
		if( $plan_number<=$real_number )
		{
			$result['result'] = -5;
			$result['message'] = '��ȯ��ʧЧ';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '��Ч';
		return $result;
	}
	
	/**
	 * �һ��ײ�ȯ
	 * @param int $user_id
	 * @param string $package_sn
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function exchange_package($user_id, $package_sn)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$user_id = intval($user_id);
		$package_sn = trim($package_sn);
		if( $user_id<1 || strlen($package_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ
		$package_info = $this->get_package_info($package_sn);
		if( empty($package_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��ȯ������';
			return $result;
		}
		$package_id = intval($package_info['package_id']);
		$cate_id = intval($package_info['cate_id']);
		
		//����Ƿ�һ���
		$exchange_info = $this->get_exchange_info($user_id, $package_sn);
		if( !empty($exchange_info) )
		{
			$result['result'] = -3;
			$result['message'] = '��ȯ����ȡ��';
			return $result;
		}
		$exchange_info = $this->get_exchange_info_by_cate_id($user_id, $cate_id);
		if( !empty($exchange_info) )
		{
			$result['result'] = -3;
			$result['message'] = '����ȯ����ȡ��';
			return $result;
		}
		
		$cur_time = time();
		
		//��������û�
		$scope_user_divide = trim($package_info['scope_user_divide']);
		if( strlen($scope_user_divide)>0 )
		{
			//����ע������
			$user_obj = POCO::singleton('pai_user_class');
			$user_info = $user_obj->get_user_info($user_id);
			$reg_time = intval($user_info['add_time']);
			$reg_time_zero = strtotime(date('Y-m-d 00:00:00', $reg_time));
			$cur_time_zero = strtotime(date('Y-m-d 00:00:00', $cur_time));
			$reg_days = ($cur_time_zero - $reg_time_zero)/86400 + 1; //ע����������Ȼ��
			$threshold_days = 15; //��ֵ����
			
			//�ж������û�
			if( $scope_user_divide=='new' ) //���û����ܶһ�
			{
				if( $reg_days>$threshold_days )
				{
					$result['result'] = -4;
					$result['message'] = '�Żݽ����������û�';
					return $result;
				}
			}
			elseif( $scope_user_divide=='old' ) //���û����ܶһ�
			{
				if( $reg_days<=$threshold_days )
				{
					$result['result'] = -4;
					$result['message'] = '�Żݽ����������û�';
					return $result;
				}
			}
			else //δ֪
			{
				$result['result'] = -4;
				$result['message'] = '��ȯ��������';
				return $result;
			}
		}
		
		//�����Ч��
		$check_ret = $this->check_package_valid($package_info, $cur_time);
		if( $check_ret['result']!=1 )
		{
			$result['result'] = -4;
			$result['message'] = $check_ret['message'];
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�ۻ�ʵ��ʹ������
		$ret = $this->margin_package_real_number($package_id, 1);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '��ȯ��ʧЧ';
			return $result;
		}
		
		//����
		$exchange_data = array(
			'user_id' => $user_id,
			'cate_id' => $cate_id,
			'package_sn' => $package_sn,
			'add_time' => $cur_time,
		);
		$exchange_id = $this->add_exchange($exchange_data);
		if( $exchange_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '��ȯ����ȡ��';
			return $result;
		}
		
		//�����Ż�ȯ
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$coupon_sn_arr = array(); //�һ����Ż�ȯ
		$ref_batch_list = $this->get_ref_batch_list($package_id, false, '', 'batch_id ASC', '');
		foreach($ref_batch_list as $ref_batch_info)
		{
			$batch_id_tmp = intval($ref_batch_info['batch_id']);
			$quantity_tmp = intval($ref_batch_info['quantity']);
			$coupon_days_tmp = intval($ref_batch_info['coupon_days']);
			
			//������Ч��������Ȼ�죩����ȡ֮����1�졣
			if( $coupon_days_tmp>0 )
			{
				$start_time_tmp = strtotime( date('Y-m-d 00:00:00', $cur_time) );
				$end_time_tmp = strtotime( date('Y-m-d 23:59:59', $cur_time+($coupon_days_tmp-1)*24*3600) );
				$more_info = array('start_time'=>$start_time_tmp, 'end_time'=>$end_time_tmp);
			}
			else
			{
				$more_info = array();
			}
			
			for($i=1; $i<=$quantity_tmp; $i++)
			{
				$give_ret = $coupon_obj->give_coupon_by_create($user_id, $batch_id_tmp, $more_info);
				if( $give_ret['result']!=1 )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());
					
					$result['result'] = -7;
					$result['message'] = '��ȯ��ʧЧ';
					return $result;
				}
				$data = array(
					'exchange_id' => $exchange_id,
					'user_id' => $user_id,
					'cate_id' => $cate_id,
					'package_sn' => $package_sn,
					'batch_id' => $batch_id_tmp,
					'coupon_sn' => $give_ret['coupon_sn'],
					'add_time' => $cur_time,
				);
				$id = $this->add_ref_coupon($data);
				if( $id<1 )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());
					
					$result['result'] = -8;
					$result['message'] = '�һ�ʧ��';
					return $result;
				}
				$coupon_sn_arr[] = $give_ret['coupon_sn'];
			}
		}
		if( empty($coupon_sn_arr) )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -9;
			$result['message'] = '��ȯ��ʧЧ';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		//�¼�����
		$pai_trigger_obj = POCO::singleton('pai_trigger_class');
		$trigger_params = array('user_id'=>$user_id, 'package_sn'=>$package_sn, 'coupon_sn_arr'=>$coupon_sn_arr);
		$pai_trigger_obj->coupon_exchange_package_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['coupon_sn'] = $coupon_sn_arr[0];
		return $result;
	}
}
