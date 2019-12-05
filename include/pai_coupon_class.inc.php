<?php
/**
 * �Ż�ȯ
 * @author Henry
 * @copyright 2015-03-02
 */

class pai_coupon_class extends POCO_TDG
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
	private function set_coupon_type_tbl()
	{
		$this->setTableName('coupon_type_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_batch_cate_tbl()
	{
		$this->setTableName('coupon_batch_cate_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_batch_tbl()
	{
		$this->setTableName('coupon_batch_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_index_tbl()
	{
		$this->setTableName('coupon_index_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_scope_tbl()
	{
		$this->setTableName('coupon_scope_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_ref_user_tbl()
	{
		$this->setTableName('coupon_ref_user_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_ref_order_tbl()
	{
		$this->setTableName('coupon_ref_order_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_supply_tbl()
	{
		$this->setTableName('coupon_supply_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_supply_user_tbl()
	{
		$this->setTableName('coupon_supply_user_tbl');
	}
	
	/**
	 * ��ȡ�Żݷ�ʽ�б�
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return int|array
	 */
	public function get_type_list($b_select_count=false, $where_str='', $order_by='type_id ASC', $limit='0,20', $fields='*')
	{
		//��ѯ
		$this->set_coupon_type_tbl();
		if( $b_select_count )
		{
			return $this->findCount($where_str);
		}
		return $this->findAll($where_str, $limit, $order_by, $fields);
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_batch_cate($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_batch_cate_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $cate_id
	 * @return boolean
	 */
	public function update_batch_cate($data, $cate_id)
	{
		//������
		$cate_id = intval($cate_id);
		if( !is_array($data) || empty($data) || $cate_id<1 )
		{
			return false;
		}
		//����
		$this->set_coupon_batch_cate_tbl();
		$affected_rows = $this->update($data, "cate_id={$cate_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ������Ϣ
	 * @param int $cate_id
	 * @return array
	 */
	public function get_batch_cate_info($cate_id)
	{
		$cate_id = intval($cate_id);
		if( $cate_id<1 )
		{
			return array();
		}
		$this->set_coupon_batch_cate_tbl();
		return $this->find("cate_id={$cate_id}");
	}
	
	/**
	 * ��ȡ���η����б�
	 * @param int $parent_id -1��ʾ������
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_batch_cate_list($parent_id=-1, $b_select_count=false, $where_str='', $order_by='sort ASC,cate_id ASC', $limit='0,20', $fields='*')
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
		$this->set_coupon_batch_cate_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ��ȡ�������η���
	 * @param int $parent_id
	 * @param int $exclude_cate_id
	 * @return array
	 */
	public function get_batch_cate_list_all($parent_id=0, $exclude_cate_id=0)
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
		$cate_list = $this->get_batch_cate_list($parent_id, false, "cate_id<>{$exclude_cate_id}", 'sort ASC,cate_id ASC', '0,99999999');
		foreach($cate_list as $cate_info)
		{
			$cate_id = intval($cate_info['cate_id']);
			$child_list = $this->get_batch_cate_list_all($cate_id, $exclude_cate_id);
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
	public function add_batch($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_batch_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $batch_id
	 * @return boolean
	 */
	public function update_batch($data, $batch_id)
	{
		//������
		$batch_id = intval($batch_id);
		if( !is_array($data) || empty($data) || $batch_id<1 )
		{
			return false;
		}
		//����
		$this->set_coupon_batch_tbl();
		$affected_rows = $this->update($data, "batch_id={$batch_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �ۻ�ʵ������
	 * @param string $where_str
	 * @param int $quantity
	 * @return boolean
	 */
	private function margin_batch_real_quantity($batch_id, $quantity=1)
	{
		$batch_id = intval($batch_id);
		$quantity = intval($quantity);
		if( $batch_id<1 || $quantity<1 )
		{
			return false;
		}
		$this->set_coupon_batch_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_quantity=real_quantity+{$quantity} WHERE batch_id={$batch_id} AND (plan_quantity-real_quantity)>={$quantity}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ������Ϣ
	 * @param int $batch_id
	 * @return array
	 */
	public function get_batch_info($batch_id)
	{
		$batch_id = intval($batch_id);
		if( $batch_id<1 )
		{
			return array();
		}
		$this->set_coupon_batch_tbl();
		return $this->find("batch_id={$batch_id}");
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
	public function get_batch_list($cate_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
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
		$this->set_coupon_batch_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ����
	 * @param int $batch_id
	 * @param string $scope_type
	 * @param string $scope_code
	 * @param string $scope_value
	 * @return boolean
	 */
	public function add_scope($batch_id, $scope_type ,$scope_code, $scope_value)
	{
		$batch_id = intval($batch_id);
		$scope_type = trim($scope_type);
		$scope_code = trim($scope_code);
		$scope_value = trim($scope_value);
		if( $batch_id<1 || !in_array($scope_type, array('white', 'black')) || strlen($scope_code)<1 )
		{
			return false;
		}
		$data = array(
			'batch_id' => $batch_id,
			'scope_type' => $scope_type,
			'scope_code' => $scope_code,
			'scope_value' => $scope_value,
		);
		$this->set_coupon_scope_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}
	
	/**
	 * ɾ��
	 * @param int $batch_id
	 * @param string $scope_type
	 * @param string $scope_code
	 * @return boolean
	 */
	public function del_scope($batch_id, $scope_type='' ,$scope_code='')
	{
		$batch_id = intval($batch_id);
		$scope_type = trim($scope_type);
		$scope_code = trim($scope_code);
		if($batch_id < 1)
		{
			return false;
		}
		$where_str = "batch_id='{$batch_id}'";
		if( strlen($scope_type)>0 )
		{
			$where_str .= ' AND scope_type=:x_scope_type';
			sqlSetParam($where_str, 'x_scope_type', $scope_type);
		}
		if( strlen($scope_code)>0 )
		{
			$where_str .= ' AND scope_code=:x_scope_code';
			sqlSetParam($where_str, 'x_scope_code', $scope_code);
		}
		$this->set_coupon_scope_tbl();
		$this->delete($where_str);
		return true;
	}
	
	/**
	 * ��ȡ
	 * @param int $batch_id
	 * @param string $scope_type
	 * @param string $scope_code
	 * @return array
	 */
	public function get_scope_info($batch_id, $scope_type, $scope_code)
	{
		$batch_id = intval($batch_id);
		$scope_type = trim($scope_type);
		$scope_code = trim($scope_code);
		if( $batch_id<1 || strlen($scope_type)<1 || strlen($scope_code)<1 )
		{
			return array();
		}
		$where_str = "batch_id='{$batch_id}' AND scope_type=:x_scope_type AND scope_code=:x_scope_code";
		sqlSetParam($where_str, 'x_scope_type', $scope_type);
		sqlSetParam($where_str, 'x_scope_code', $scope_code);
		$this->set_coupon_scope_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $batch_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_scope_list($batch_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$batch_id = intval($batch_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $batch_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "batch_id={$batch_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_coupon_scope_tbl();
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
	private function add_coupon($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_index_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �����ѷ���
	 * @param string $coupon_sn
	 * @param array $more_info array('give_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_give($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_give' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_give=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ������ʹ��
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ����δʹ��
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_not_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 0,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1 AND is_cash=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �����Ѷ���
	 * @param string $coupon_sn
	 * @param array $more_info array('cash_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_cash($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_cash' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1 AND is_cash=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �����ѽ���
	 * @param string $coupon_sn
	 * @param array $more_info array('cash_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_settle($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_settle' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1 AND is_cash=1 AND is_settle=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param string $coupon_sn
	 * @return array
	 */
	public function get_coupon_info($coupon_sn)
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		$where_str = 'coupon_sn=:x_coupon_sn';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $coupon_id
	 * @return array
	 */
	public function get_coupon_info_by_id($coupon_id)
	{
		$coupon_id = intval($coupon_id);
		if( $coupon_id<1 )
		{
			return array();
		}
		$this->set_coupon_index_tbl();
		return $this->find("coupon_id={$coupon_id}");
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $batch_id
	 * @return array
	 */
	private function get_coupon_info_for_give($batch_id)
	{
		$batch_id = intval($batch_id);
		if( $batch_id<1 )
		{
			return array();
		}
		$this->set_coupon_index_tbl();
		return $this->find("batch_id={$batch_id} AND is_give=0", "coupon_id ASC");
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $batch_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_coupon_list($batch_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$batch_id = intval($batch_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $batch_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "batch_id={$batch_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_coupon_index_tbl();
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
	private function add_ref_user($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return false;
		}
		$this->set_coupon_ref_user_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ������ʹ��
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_ref_user_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_user_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ����δʹ��
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_ref_user_not_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 0,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_user_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param string $coupon_sn
	 * @return array
	 */
	public function get_ref_user_info_by_sn($coupon_sn)
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		$where_str = "coupon_sn=:x_coupon_sn";
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_user_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ�����û��б�
	 * @param int $user_id
	 * @param int $batch_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_ref_user_list($user_id, $batch_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$user_id = intval($user_id);
		$batch_id = intval($batch_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "user_id={$user_id}";
		}
		
		if( $batch_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "batch_id={$batch_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_coupon_ref_user_tbl();
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
	private function add_ref_order($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_ref_order_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �������˻�
	 * @param int $id
	 * @param array $more_info array('refund_time'=>0)
	 * @return boolean
	 */
	private function update_ref_order_refund($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_refund' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �����Ѷ���
	 * @param int $id
	 * @param double $cash_amount
	 * @param array $more_info array('cash_time'=>0)
	 * @return boolean
	 */
	private function update_ref_order_cash($id, $cash_amount, $more_info=array())
	{
		$id = intval($id);
		$cash_amount = $cash_amount*1;
		if( $id<1 || $cash_amount<0 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'cash_amount' => $cash_amount,
			'is_cash' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND used_amount>={$cash_amount} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �����ѽ���
	 * @param int $id
	 * @param array $more_info array('settle_time'=>0)
	 * @return boolean
	 */
	private function update_ref_order_settle($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_settle' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_refund=0 AND is_cash=1 AND is_settle=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ɾ��
	 * @param int $id
	 * @return boolean
	 */
	private function del_ref_order($id)
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->delete("id={$id} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ�б�
	 * @param string $order_type
	 * @param int $order_rid
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_ref_order_list($b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		//��ѯ
		$this->set_coupon_ref_order_tbl();
		if( $b_select_count )
		{
			return $this->findCount($where_str);
		}
		$list = $this->findAll($where_str, $limit, $order_by, $fields);
		return $list;
	}
	
	/**
	 * ��ȡ�б�
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return array
	 */
	public function get_ref_order_list_by_oid($channel_module, $channel_oid)
	{
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			return array();
		}
		$where_str = "channel_module=:x_channel_module AND channel_oid=:x_channel_oid";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_channel_oid', $channel_oid);
		return $this->get_ref_order_list(false, $where_str, 'id ASC', '0,99999999');
	}
	
	/**
	 * �ϼ��Żݽ��
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return string ����2λС��
	 */
	public function sum_ref_order_used_amount_by_oid($channel_module, $channel_oid)
	{
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND channel_oid=:x_channel_oid";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_channel_oid', $channel_oid);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(used_amount) AS used_amount");
		return number_format($row['used_amount']*1, 2, '.', '');
	}
	
	/**
	 * �ϼƶ��ֽ��
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return string ����2λС��
	 */
	public function sum_ref_order_cash_amount_by_oid($channel_module, $channel_oid)
	{
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND channel_oid=:x_channel_oid";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_channel_oid', $channel_oid);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(cash_amount) AS cash_amount");
		return number_format($row['cash_amount']*1, 2, '.', '');
	}
	
	/**
	 * �ϼƶ��ֽ��
	 * @param string $channel_module
	 * @param int $event_id
	 * @return string ����2λС��
	 */
	public function sum_ref_event_cash_amount_by_event_id($channel_module, $event_id)
	{
		$channel_module = trim($channel_module);
		$event_id = intval($event_id);
		if( strlen($channel_module)<1 || $event_id<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND event_id=:x_event_id";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_event_id', $event_id);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(cash_amount) AS cash_amount");
		return number_format($row['cash_amount']*1, 2, '.', '');
	}
	
	/**
	 * �ϼ����ζ��ֽ��
	 * @param int|array $batch_ids
	 * @return string ����2λС��
	 */
	public function sum_ref_order_cash_amount_by_batch_id($batch_ids)
	{
		$batch_id_arr = array();
		
		//������
		if( !is_array($batch_ids) )
		{
			$batch_ids = array($batch_ids);
		}
		foreach($batch_ids as $val)
		{
			$val = intval($val);
			if( $val>0 ) $batch_id_arr[] = $val;
		}
		if( empty($batch_id_arr) )
		{
			return 0;
		}
		$batch_id_str = implode(',', $batch_id_arr);
		$where_str = "batch_id IN ({$batch_id_str}) AND is_cash=1";
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(cash_amount) AS cash_amount");
		return number_format($row['cash_amount']*1, 2, '.', '');
	}
	
	/**
	 * �ϼ������ߵĶ��ֽ��
	 * @param string $channel_module
	 * @param int $event_id
	 * @param int $in_user_id
	 * @return string ����2λС��
	 */
	public function sum_ref_order_in_amount_by_in_user_id($channel_module, $event_id, $in_user_id)
	{
		$channel_module = trim($channel_module);
		$event_id = intval($event_id);
		$in_user_id = intval($in_user_id);
		if( strlen($channel_module)<1 || $event_id<1 || $in_user_id<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND event_id={$event_id} AND in_user_id={$in_user_id}";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(in_amount) AS in_amount");
		return number_format($row['in_amount']*1, 2, '.', '');
	}
	
	/**
	 * �ϼƻ����Ķ��ֽ��
	 * @param string $channel_module
	 * @param int $event_id
	 * @param int $in_user_id
	 * @param int $org_user_id
	 * @return string ����2λС��
	 */
	public function sum_ref_order_org_amount_by_org_user_id($channel_module, $event_id, $in_user_id, $org_user_id)
	{
		$channel_module = trim($channel_module);
		$event_id = intval($event_id);
		$in_user_id = intval($in_user_id);
		$org_user_id = intval($org_user_id);
		if( strlen($channel_module)<1 || $event_id<1 || $in_user_id<1 || $org_user_id<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND event_id={$event_id} AND in_user_id={$in_user_id} AND org_user_id={$org_user_id}";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(org_amount) AS org_amount");
		return number_format($row['org_amount']*1, 2, '.', '');
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $id
	 * @return array
	 */
	public function get_ref_order_info($id)
	{
		$id = intval($id);
		if( $id<1 )
		{
			return array();
		}
		$this->set_coupon_ref_order_tbl();
		return $this->find("id={$id}");
	}
	
	/**
	 * ��ȡ����������ʹ���˴��Ż�ȯ
	 * @param string $coupon_sn
	 * @return array
	 */
	private function get_ref_order_info_by_sn($coupon_sn)
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		$where_str = "coupon_sn=:x_coupon_sn AND is_refund=0";
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_order_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡδ���������
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $where_str
	 * @return string
	 */
	private function get_unsettle_where_str($org_user_id, $channel_module='', $where_str='')
	{
		$org_user_id = intval($org_user_id);
		$channel_module = trim($channel_module);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $org_user_id>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id={$org_user_id}";
		}
		else
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id>0";
		}
		
		//Լ�Ļ�����
		if( strlen($channel_module)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "channel_module=:x_channel_module";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
		}
		
		//δ�˻�
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_refund=0";
		
		//�Ѷ���
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_cash=1";
		
		//δ����
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_settle=0";
		
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		return $sql_where;
	}
	
	/**
	 * ��ȡ������δ������
	 * @param int $org_user_id
	 * @param string $channel_module yuepaiԼ�� waipai����
	 * @param string $where_str
	 * @return double
	 */
	public function get_unsettle_org_amount($org_user_id, $channel_module='', $where_str='')
	{
		$sql_where = $this->get_unsettle_where_str($org_user_id, $channel_module, $where_str);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($sql_where, null, "SUM(org_amount) AS org_amount");
		return number_format($row['org_amount']*1, 2, '.', '');
	}
	
	/**
	 * ��ȡ������δ�����б�
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_unsettle_ref_order_list($org_user_id, $channel_module='', $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields = '*')
	{
		$sql_where = $this->get_unsettle_where_str($org_user_id, $channel_module, $where_str);
		return $this->get_ref_order_list($b_select_count, $sql_where, $order_by, $limit, $fields);
	}
	
	/**
	 * ��ȡ�ѽ��������
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $where_str
	 * @return string
	 */
	private function get_settle_where_str($org_user_id, $channel_module='', $where_str='')
	{
		$org_user_id = intval($org_user_id);
		$channel_module = trim($channel_module);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $org_user_id>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id={$org_user_id}";
		}
		else
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id>0";
		}
		
		//Լ�Ļ�����
		if( strlen($channel_module)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "channel_module=:x_channel_module";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
		}
		
		//δ�˻�
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_refund=0";
		
		//�Ѷ���
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_cash=1";
		
		//�ѽ���
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_settle=1";
		
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		return $sql_where;
	}
	
	/**
	 * ��ȡ�������ѽ�����
	 * @param int $org_user_id
	 * @param string $channel_module yuepaiԼ�� waipai����
	 * @param string $where_str
	 * @return double
	 */
	public function get_settle_org_amount($org_user_id, $channel_module='', $where_str='')
	{
		$sql_where = $this->get_settle_where_str($org_user_id, $channel_module, $where_str);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($sql_where, null, "SUM(org_amount) AS org_amount");
		return number_format($row['org_amount']*1, 2, '.', '');
	}
	
	/**
	 * ��ȡ�������ѽ����б�
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_settle_ref_order_list($org_user_id, $channel_module='', $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields = '*')
	{
		$sql_where = $this->get_settle_where_str($org_user_id, $channel_module, $where_str);
		return $this->get_ref_order_list($b_select_count, $sql_where, $order_by, $limit, $fields);
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_supply($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_supply_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $supply_id
	 * @return array
	 */
	private function get_supply_info($supply_id)
	{
		$supply_id = intval($supply_id);
		if( $supply_id<1 )
		{
			return array();
		}
		$this->set_coupon_supply_tbl();
		return $this->find("supply_id={$supply_id}");
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_supply_user($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_supply_user_tbl();
		return $this->insert($data, 'IGNORE');
	}
		
	/**
	 * ��������Ϣ֪ͨ
	 * @param int $id
	 * @param array $more_info array('message_time'=>0)
	 * @return boolean
	 */
	private function update_supply_user_message($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_message' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_supply_user_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_message=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��������ȡ
	 * @param int $id
	 * @param array $more_info array('coupon_sn'=>'', 'give_time'=>0)
	 * @return boolean
	 */
	private function update_supply_user_give($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_give' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_supply_user_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_give=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $supply_id
	 * @param int $user_id
	 * @return array
	 */
	public function get_supply_user_info($supply_id, $user_id)
	{
		$supply_id = intval($supply_id);
		$user_id = intval($user_id);
		if( $supply_id<1 || $user_id<1 )
		{
			return array();
		}
		$this->set_coupon_supply_user_tbl();
		return $this->find("supply_id={$supply_id} AND user_id={$user_id}");
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
		//��һλ�涨��1��Ԥ��2��9Ϊ�Ժ���չ��
		$code = '1';
		$length--;
		$pattern = '5348029716';
		for($i=0; $i<$length; $i++)
		{
			$code .= substr($pattern, mt_rand(0,9), 1);
		}
		return $code;
	}
	
	/**
	 * ��������
	 * @param array $data
	 * @return int �ɹ��򷵻�����ID��ʧ���򷵻�0
	 */
	public function create_batch_old($data)
	{
		//������
		$cate_id = intval($data['cate_id']);
		$batch_name = trim($data['batch_name']);
		$coupon_type_id = intval($data['coupon_type_id']);
		$coupon_face_value = number_format($data['coupon_face_value']*1, 2, '.', '')*1;
		$coupon_start_time = intval($data['coupon_start_time']);
		$coupon_end_time = intval($data['coupon_end_time']);
		$scope_module_type = trim($data['scope_module_type']);
		$scope_order_total_amount = number_format($data['scope_order_total_amount'], 2, '.', '')*1;
		$scope_model_user_id = trim($data['scope_model_user_id']);
		$scope_org_user_id = trim($data['scope_org_user_id']);
		$scope_event_id = trim($data['scope_event_id']);
		$scope_event_user_id = trim($data['scope_event_user_id']);
		$scope_service_id = trim($data['scope_service_id']);
		$scope_mall_type_id = trim($data['scope_mall_type_id']);
		$scope_seller_user_id = trim($data['scope_seller_user_id']);
		if( $cate_id<1 || strlen($batch_name)<1 || $coupon_type_id<1 || $coupon_face_value<=0 || $coupon_start_time<1 || $coupon_end_time<1 )
		{
			return 0;
		}
		//��������
		unset($data['scope_event_user_id']);
		unset($data['scope_mall_type_id']);
		unset($data['scope_seller_user_id']);
		$batch_data = $data;
		$batch_data['cate_id'] = $cate_id;
		$batch_data['batch_name'] = $batch_name;
		$batch_data['coupon_type_id'] = $coupon_type_id;
		$batch_data['coupon_face_value'] = $coupon_face_value;
		$batch_data['coupon_start_time'] = $coupon_start_time;
		$batch_data['coupon_end_time'] = $coupon_end_time;
		$batch_data['scope_module_type'] = $scope_module_type;
		$batch_data['scope_order_total_amount'] = $scope_order_total_amount;
		$batch_data['scope_model_user_id'] = $scope_model_user_id;
		$batch_data['scope_org_user_id'] = $scope_org_user_id;
		$batch_data['scope_event_id'] = $scope_event_id;
		if( !isset($batch_data['add_time']) ) $batch_data['add_time'] = time();
		$batch_id = $this->add_batch($batch_data);
		if( $batch_id<1 )
		{
			return 0;
		}
		//���淶Χ���ɲ�������check_scope_code��˵��
		if( strlen($scope_module_type)>0 )
		{
			$this->add_scope($batch_id, 'white', 'module_type', $scope_module_type);
		}
		if( $scope_order_total_amount>0 )
		{
			$this->add_scope($batch_id, 'white', 'order_total_amount', $scope_order_total_amount);
		}
		if( strlen($scope_model_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'model_user_id', $scope_model_user_id);
		}
		if( strlen($scope_org_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'org_user_id', $scope_org_user_id);
		}
		if( strlen($scope_event_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'event_id', $scope_event_id);
		}
		if( strlen($scope_event_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'event_user_id', $scope_event_user_id);
		}
		if( strlen($scope_service_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'service_id', $scope_service_id);
		}
		if( strlen($scope_mall_type_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'mall_type_id', $scope_mall_type_id);
		}
		if( strlen($scope_seller_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'seller_user_id', $scope_seller_user_id);
		}
		return $batch_id;
	}
	
	/**
	 * ��������
	 * @param array $data
	 * @return int �ɹ��򷵻�����ID��ʧ���򷵻�0
	 */
	public function create_batch($data)
	{
		//������
		$cate_id = intval($data['cate_id']);
		$batch_name = trim($data['batch_name']);
		$coupon_type_id = intval($data['coupon_type_id']);
		$coupon_face_value = number_format($data['coupon_face_value']*1, 2, '.', '')*1;
		$coupon_start_time = intval($data['coupon_start_time']);
		$coupon_end_time = intval($data['coupon_end_time']);
		$scope_module_type = trim($data['scope_module_type']);
		$scope_order_total_amount = number_format($data['scope_order_total_amount'], 2, '.', '')*1;
		if( $cate_id<1 || strlen($batch_name)<1 || $coupon_type_id<1 || $coupon_face_value<=0 || $coupon_start_time<1 || $coupon_end_time<1 )
		{
			return 0;
		}
		//��������
		$batch_data = $data;
		$batch_data['cate_id'] = $cate_id;
		$batch_data['batch_name'] = $batch_name;
		$batch_data['coupon_type_id'] = $coupon_type_id;
		$batch_data['coupon_face_value'] = $coupon_face_value;
		$batch_data['coupon_start_time'] = $coupon_start_time;
		$batch_data['coupon_end_time'] = $coupon_end_time;
		$batch_data['scope_module_type'] = $scope_module_type;
		$batch_data['scope_order_total_amount'] = $scope_order_total_amount;
		if( !isset($batch_data['add_time']) ) $batch_data['add_time'] = time();
		return $this->add_batch($batch_data);
	}
	
	/**
	 * �����Ż�ȯ
	 * @param int $batch_id	����ID
	 * @param array $more_info ������׷���Ż�ȯ��Ϣ array('start_time'=>0, 'end_time'=>0)
	 * @return array array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function create_coupon($batch_id, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		//������
		$batch_id = intval($batch_id);
		if( $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		
		//��ȡ������Ϣ
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -2;
			$result['message'] = '���β�����';
			return $result;
		}
		$type_id = intval($batch_info['coupon_type_id']);
		$face_value = number_format($batch_info['coupon_face_value']*1, 2, '.', '')*1;
		$face_max = number_format($batch_info['coupon_face_max']*1, 2, '.', '')*1;
		$start_time = intval($batch_info['coupon_start_time']);
		$end_time = intval($batch_info['coupon_end_time']);
		if( isset($more_info['start_time']) ) $start_time = intval($more_info['start_time']);
		if( isset($more_info['end_time']) ) $end_time = intval($more_info['end_time']);
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�ۻ��Ż�ȯʵ������
		$ret = $this->margin_batch_real_quantity($batch_id, 1);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -3;
			$result['message'] = '�ѴﵽԤ������';
			return $result;
		}
		
		$coupon_id = 0;
		$coupon_sn = '';
		$while_count = 0;
		while($while_count<9999)
		{
			$rand_str = $this->get_rand_str(16);
			if( strlen($rand_str)<1 )
			{
				//�Ż�ȯ��Ϊ��
				break;
			}
			$tmp = $this->get_coupon_info($rand_str);
			if( !empty($tmp) )
			{
				//�Ż�ȯ���Ѵ���
				$while_count++;
				continue;
			}
			$coupon_data = array(
				'coupon_sn' => $rand_str,
				'batch_id' => $batch_id,
				'type_id' => $type_id,
				'face_value' => $face_value,
				'face_max' => $face_max,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'add_time' => time(),
			);
			$id = $this->add_coupon($coupon_data);
			if( $id<1 )
			{
				//��������һ������Ҳ�������Ż�ȯ
				$while_count++;
				continue;
			}
			$coupon_id = $id;
			$coupon_sn = $rand_str;
			break;
		}
		if( $coupon_id<1 || strlen($coupon_sn)<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * ���ɶ���Ż�ȯ
	 * @param int $batch_id ����ID
	 * @param int $quantity �˴���������
	 * @param array $more_info ������׷���Ż�ȯ��Ϣ array('start_time'=>0, 'end_time'=>0)
	 * @return array array('result'=>0, 'message'=>'', 'quantity'=>0)
	 */
	public function create_coupon_multiple($batch_id, $quantity, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'quantity'=>0);
		
		$batch_id = intval($batch_id);
		$quantity = intval($quantity);
		if( $batch_id<1 || $quantity<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -2;
			$result['message'] = '���β�����';
			return $result;
		}
		
		//����˴���������
		$plan_quantity = intval($batch_info['plan_quantity']); //�ƻ�����
		$real_quantity = intval($batch_info['real_quantity']); //ʵ������
		$remain_quantity = $plan_quantity - $real_quantity; //ʣ������
		if( $remain_quantity<1 )
		{
			$result['result'] = -3;
			$result['message'] = '�ѴﵽԤ������';
			return $result;
		}
		if( $quantity>$remain_quantity ) $quantity = $remain_quantity;
		
		//�����Ż�ȯ
		$create_quantity = 0; //ʵ����������
		for($i=1; $i<=$quantity; $i++)
		{
			$create_ret = $this->create_coupon($batch_id, $more_info);
			if( $create_ret['result']!=1 )
			{
				break;
			}
			$create_quantity++;
		}
		if( $create_quantity<1 )
		{
			$result['result'] = -4;
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
	 * �����Ż�ȯ
	 * @param int $user_id
	 * @param string $coupon_sn
	 * @param boolean $b_valid �Ƿ����Ż�ȯ��Ч��
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function give_coupon($user_id, $coupon_sn, $b_valid=true)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		//������
		$user_id = intval($user_id);
		$coupon_sn = trim($coupon_sn);
		if( $user_id<1 || strlen($coupon_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//�����Ż��룬����ĵ��ײ��봦��
		if( !preg_match('/^[0-8]\d{15}$/isU', $coupon_sn) )
		{
			$coupon_package_obj = POCO::singleton('pai_coupon_package_class');
			return $coupon_package_obj->exchange_package($user_id, $coupon_sn);
		}
		
		//��ȡ�Ż�ȯ��Ϣ
		$coupon_info = $this->get_coupon_info($coupon_sn);
		if( empty($coupon_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��ȯ������';
			return $result;
		}
		if( $coupon_info['is_give']!=0 )
		{
			$result['result'] = -3;
			$result['message'] = '��ȯ�ѷ���';
			return $result;
		}
		if( $b_valid )
		{
			$valid_ret = $this->check_coupon_valid($coupon_info);
			if( $valid_ret['result']!=1 )
			{
				$result['result'] = -4;
				$result['message'] = $valid_ret['message'];
				return $result;
			}
		}
		
		$cur_time = time();
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�����ѷ���
		$ret = $this->update_coupon_give($coupon_sn, array('give_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '��ȯ�ѷ���';
			return $result;
		}
		
		//��������
		$ref_data = array(
			'user_id' => $user_id,
			'coupon_sn' => $coupon_sn,
			'batch_id' => $coupon_info['batch_id'],
			'type_id' => $coupon_info['type_id'],
			'start_time' => $coupon_info['start_time'],
			'end_time' => $coupon_info['end_time'],
			'is_used' => $coupon_info['is_used'],
			'used_time' => $coupon_info['used_time'],
			'add_time' => $cur_time,
		);
		$id = $this->add_ref_user($ref_data);
		if( $id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '��ȯ����ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * ���ɲ������Ż�ȯ
	 * @param int $user_id
	 * @param int $batch_id ����ID
	 * @param array $more_info ������׷���Ż�ȯ��Ϣ array('start_time'=>0, 'end_time'=>0)
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function give_coupon_by_create($user_id, $batch_id, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$user_id = intval($user_id);
		$batch_id = intval($batch_id);
		if( $user_id<1 || $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��������Ƿ����
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) || $batch_info['check_status']!=1 )
		{
			$result['result'] = -1;
			$result['message'] = '����δ���';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�����Ż���
		$create_ret = $this->create_coupon($batch_id, $more_info);
		if( $create_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -2;
			$result['message'] = '����ʧ��';
			return $result;
		}
		$coupon_sn = trim($create_ret['coupon_sn']);
		
		//�����Ż�ȯ
		$give_ret = $this->give_coupon($user_id, $coupon_sn, false);
		if( $give_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -3;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * ���Ҳ������Ż�ȯ
	 * @param int $user_id
	 * @param int $batch_id ����ID
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function give_coupon_by_find($user_id, $batch_id)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$user_id = intval($user_id);
		$batch_id = intval($batch_id);
		if( $user_id<1 || $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��������Ƿ����
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) || $batch_info['check_status']!=1 )
		{
			$result['result'] = -1;
			$result['message'] = '����δ���';
			return $result;
		}
		
		//����ѭ�����ԣ�û�в�������
		$coupon_sn = '';
		$coupon_sn_tmp = '';
		$while_count = 0;
		while($while_count<9999)
		{
			//�����Ż���
			$coupon_info_tmp = $this->get_coupon_info_for_give($batch_id);
			if( empty($coupon_info_tmp) )
			{
				//û���Ż�ȯ��
				$result['result'] = -2;
				$result['message'] = '��������';
				return $result;
			}
			if( $coupon_sn_tmp==$coupon_info_tmp['coupon_sn'] )
			{
				//���ϴ�ѭ����һ��
				break;
			}
			$coupon_sn_tmp = trim($coupon_info_tmp['coupon_sn']);
			
			//�����Ż�ȯ
			$give_ret = $this->give_coupon($user_id, $coupon_sn_tmp, false);
			if( $give_ret['result']!=1 )
			{
				$while_count++;
				continue;
			}
			
			$coupon_sn = $coupon_sn_tmp;
			break;
		}
		if( strlen($coupon_sn)<1 )
		{
			$result['result'] = -3;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * ����Ż�ȯ����Ч��
	 * �˷��������is_give�������м���ж�
	 * @param array $coupon_info
	 * @param int $cur_time ��ǰʱ��
	 * @param bool $check_is_used �Ƿ���
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function check_coupon_valid($coupon_info, $cur_time=0, $check_is_used=true)
	{
		$result = array('result'=>0, 'message'=>'');
		
		if( !is_array($coupon_info) || empty($coupon_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��ȯ������';
			return $result;
		}
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
		
		$coupon_sn = trim($coupon_info['coupon_sn']);
		if( strlen($coupon_sn)<1 )
		{
			$result['result'] = -2;
			$result['message'] = '��ȯ������';
			return $result;
		}
		
		$is_used = intval($coupon_info['is_used']);
		if( $check_is_used && $is_used==1 )
		{
			$result['result'] = -3;
			$result['message'] = '��ȯ��ʹ��';
			return $result;
		}
		
		$is_cash = intval($coupon_info['is_cash']);
		if( $is_cash==1 )
		{
			$result['result'] = -4;
			$result['message'] = '��ȯ��ʹ��';
			return $result;
		}
		
		$start_time = intval($coupon_info['start_time']);
		if( $start_time>$cur_time )
		{
			$result['result'] = -5;
			$result['message'] = '��ȯδ����Ч��';
			return $result;
		}
		
		$end_time = intval($coupon_info['end_time']);
		if( $end_time<$cur_time )
		{
			$result['result'] = -6;
			$result['message'] = '��ȯ�ѹ���Ч��';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '��Ч';
		return $result;
	}
	
	/**
	 * ��ȡ���÷�Χ����
	 * @return array
	 */
	public function get_scope_code_arr()
	{
		return array(
			'all' => 'ȫ��',
			'order_total_amount' => '�������',
			'org_user_id' => '�����û�ID',
			'module_type' => 'ģ������',
			'mall_order_type' => '�������ͣ��̳�ϵͳ��',
			'mall_type_id' => '����Ʒ��ID���̳�ϵͳ��',
			'seller_user_id' => '�̼��û�ID���̳�ϵͳ������ϵͳ��',
			'mall_goods_id' => '��ƷID���̳�ϵͳ��',
			'mall_stage_id' => '�����ID���̳�ϵͳ��',
			'event_id' => '�ID������ϵͳ��',
			'event_user_id' => '��֯���û�ID������ϵͳ��',
			'model_user_id' => 'ģ���û�ID��Լ��ϵͳ��',
			'location_id' => '����ID������ϵͳ��',
			'service_id' => '��������ID��TTϵͳ��',
		);
	}
	
	/**
	 * ����Ƿ����ڷ�Χ��
	 * @param array $scope_info
	 * @param array $param_info
	 * @param int $cur_time ��ǰʱ��
	 * @return boolean û������������󣬷���false
	 * @tutorial
	 * 
	 * $scope_code $scope_value������
	 * module_type yuepai#waipai#topic ģ������
	 * order_amount 5.00 �������
	 * model_user_id 1#2 ģ���û�ID
	 * org_user_id 1#2 �����û�ID
	 * event_id 1#2 �ID
	 * 
	 */
	private function check_scope_code($scope_info, $param_info, $cur_time=0)
	{
		$result = false;
		
		//������
		if( !is_array($scope_info) || empty($scope_info) )
		{
			return $result;
		}
		if( !is_array($param_info) ) $param_info = array();
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
		
		//����Χ
		$scope_code = strtolower(trim($scope_info['scope_code']));
		$scope_value = trim($scope_info['scope_value']);
		
		$scope_value_arr = array();
		$scope_value_arr_tmp = explode('#', $scope_value);
		foreach ($scope_value_arr_tmp as $val)
		{
			$val = trim($val);
			if( strlen($val)<1 ) continue;
			$scope_value_arr[] = $val;
		}
		unset($val, $scope_value_arr_tmp);
		
		//�жϷ�Χ
		switch($scope_code)
		{
			case 'all':
				$result = true;
				break;
			case 'module_type':
				$module_type = trim($param_info['module_type']);
				if( in_array($module_type, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'order_total_amount':
				$order_total_amount = $param_info['order_total_amount']*1;
				if( $order_total_amount>0 && $order_total_amount>=$scope_value*1 )
				{
					$result = true;
				}
				break;
			case 'model_user_id':
				$model_user_id = trim($param_info['model_user_id']);
				if( in_array($model_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'org_user_id':
				$org_user_id = trim($param_info['org_user_id']);
				if( in_array($org_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'location_id':
				$location_id_str = trim($param_info['location_id']);
				$location_id_arr = explode(',', $location_id_str);
				foreach($location_id_arr as $location_id)
				{
					$location_id = trim($location_id);
					if( strlen($location_id)<1 ) continue;
					foreach($scope_value_arr as $tmp)
					{
						if( preg_match('/^\d+$/isU', $tmp) && preg_match('/^'.$tmp.'/isU', $location_id) )
						{
							$result = true;
						}
					}
				}
				break;
			case 'event_id':
				$event_id = trim($param_info['event_id']);
				if( in_array($event_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'event_user_id':
				$event_user_id = trim($param_info['event_user_id']);
				if( in_array($event_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'service_id':
				$service_id = trim($param_info['service_id']);
				if( in_array($service_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_type_id':
				$mall_type_id = trim($param_info['mall_type_id']);
				if( in_array($mall_type_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'seller_user_id':
				$seller_user_id = trim($param_info['seller_user_id']);
				if( in_array($seller_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_goods_id':
				$mall_goods_id = trim($param_info['mall_goods_id']);
				if( in_array($mall_goods_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_order_type':
				$mall_order_type = trim($param_info['mall_order_type']);
				if( in_array($mall_order_type, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_stage_id':
				$mall_stage_id = trim($param_info['mall_stage_id']);
				if( in_array($mall_stage_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			default:
				break;
		}
		
		return $result;
	}
	
	/**
	 * ������÷�Χ
	 * @param array $batch_info
	 * @param array $param_info
	 * @param int $cur_time ��ǰʱ��
	 * @return array array('result'=>0, 'message'=>'', 'message_white_false'=>'', 'message_black_true'=>'')
	 */
	private function check_coupon_scope($batch_info, $param_info, $cur_time=0)
	{
		$result = array('result'=>0, 'message'=>'', 'message_white_false'=>'', 'message_black_true'=>'');
		
		$batch_id = intval($batch_info['batch_id']);
		if( !is_array($batch_info) || empty($batch_info) || $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '���β�����';
			return $result;
		}
		if( !is_array($param_info) ) $param_info = array();
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
		
		//������״̬
		$check_status = intval($batch_info['check_status']);
		if( $check_status!=1 )
		{
			$result['result'] = -2;
			$result['message'] = '�������״̬����';
			return $result;
		}
		
		//��ȡ����
		$scope_list = $this->get_scope_list($batch_id, false, '', '', '0,99999999');
		if( !is_array($scope_list) || empty($scope_list) )
		{
			$result['result'] = -3;
			$result['message'] = '����û�����÷�Χ';
			return $result;
		}
		
		//����ڰ�����
		$white_list = array();
		$black_list = array();
		foreach ($scope_list as $val)
		{
			$scope_type = strtolower(trim($val['scope_type']));
			if( $scope_type=='white' )
			{
				$white_list[] = $val;
			}
			elseif( $scope_type=='black' )
			{
				$black_list[] = $val;
			}
		}
		if( empty($white_list) && empty($black_list) )
		{
			$result['result'] = -4;
			$result['message'] = '�������÷�Χ����';
			return $result;
		}
		
		//��������
		$ret_white = true;
		$white_info_false = array();
		foreach ($white_list as $white_info)
		{
			$ret = $this->check_scope_code($white_info, $param_info, $cur_time);
			if( !$ret )
			{
				//���������ķ�Χ�ڣ�������
				$ret_white = false;
				$white_info_false = $white_info;
				break;
			}
		}
		
		//��������
		$ret_black = false;
		$black_info_true = array();
		foreach ($black_list as $black_info)
		{
			$ret = $this->check_scope_code($black_info, $param_info, $cur_time);
			if( $ret )
			{
				//�������ķ�Χ�ڣ�������
				$ret_black = true;
				$black_info_true = $black_info;
				break;
			}
		}
		
		if( $ret_white==true && $ret_black==false)
		{
			$result['result'] = 1;
			$result['message'] = '����';
		}
		else
		{
			$result['result'] = -5;
			$result['message'] = '������';
			$result['message_white_false'] = http_build_query($white_info_false);
			$result['message_black_true'] = http_build_query($black_info_true);
		}
		return $result;
	}
	
	/**
	 * ����ر�����
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @param int $cur_time
	 * @return array
	 */
	private function check_coupon_limit($user_id, $type_id, $param_info, $cur_time=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		$cur_time = intval($cur_time);
		if( $user_id<1 || $type_id<1 || !is_array($param_info) || empty($param_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( $cur_time<1 ) $cur_time = time();
		
		//Լ�ĵ�
		if( $param_info['channel_module']=='yuepai' )
		{
			$model_user_id_tmp = intval($param_info['model_user_id']);
			
			//���ģ���Ƿ������
			$model_audit_obj = POCO::singleton('pai_model_audit_class');
			$is_approval_tmp = $model_audit_obj->check_model_is_approval_for_coupon($model_user_id_tmp);
			if( !$is_approval_tmp && !in_array($model_user_id_tmp, array(100028, 103511)) )
			{
				$result['result'] = -2;
				$result['message'] = '���ڸ�ģ��δ��ϵͳ��ˣ����ν����޷�ʹ���Ż݄�';
				return $result;
			}
			
			//�������Ż�ȯʹ�ô���
			$date_id_str = '';
			$date_id_sp = '';
			$today_begin_time = strtotime(date('Y-m-d 00:00:00', $cur_time));
			$today_end_time = strtotime(date('Y-m-d 23:59:59', $cur_time));
			$channel_oid_list_tmp = $this->get_ref_order_list(false, "channel_module='yuepai' AND user_id={$user_id} AND type_id=1 AND is_refund=0 AND {$today_begin_time}<=add_time AND add_time<={$today_end_time}", '', '0,99999999', 'channel_oid');
			foreach($channel_oid_list_tmp as $channel_oid_info_tmp)
			{
				$date_id_str .= $date_id_sp . $channel_oid_info_tmp['channel_oid']*1;
				$date_id_sp = ',';
			}
			if( strlen($date_id_str)>0 )
			{
				include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
				$event_date_obj = POCO::singleton('event_date_class');
				
				//���⴦�������տ�ģ��113621
				if( in_array($model_user_id_tmp, array(113621)) )
				{
					$today_tmp = $event_date_obj->get_all_event_date(true, "date_id IN ({$date_id_str}) AND pay_status=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time} AND to_date_id={$model_user_id_tmp}");
					if( $today_tmp>=3 )
					{
						$result['result'] = -3;
						$result['message'] = '���߿γ��Ż�ȯÿ��ֻ��ʹ������';
						return $result;
					}
				}
				else
				{
					$today_tmp = $event_date_obj->get_all_event_date(true, "date_id IN ({$date_id_str}) AND pay_status=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time}");
					if( $today_tmp>=1 )
					{
						$result['result'] = -4;
						$result['message'] = 'Լ���Ż�ȯÿ��ֻ��ʹ��һ�ţ�ÿ��0��00Ϊ����ʱ��';
						return $result;
					}
				}
			}
		}
        elseif( $param_info['channel_module']=='mall_order' && $param_info['mall_order_type']=='activity' )//���
        {
            $activity_id = intval($param_info['mall_goods_id']);
            $stage_id = intval($param_info['mall_stage_id']);
            if( $activity_id<1 || $stage_id<1 )
            {
                $result['result'] = -6;
                $result['message'] = '��������';
                return $result;
            }
            $mall_order_obj = POCO::singleton('pai_mall_order_class');
            $count = $mall_order_obj->get_order_list_by_activity_stage($activity_id, $stage_id, -1, true, "is_pay=1 AND is_use_coupon=1 AND buyer_user_id={$user_id}");

            if( $count>=1 )
            {
                $result['result'] = -6;
                $result['message'] = '�˳���ֻ��ʹ��һ���Ż�ȯ';
                return $result;
            }
        }
		elseif( $param_info['channel_module']=='mall_order' ) //�̳ǵ�
		{
			$mall_type_id_tmp = intval($param_info['mall_type_id']);
			$seller_user_id_tmp = intval($param_info['seller_user_id']);
			
			//�������Ż�ȯʹ�ô���
			$order_id_str = '';
			$order_id_sp = '';
			$today_begin_time = strtotime(date('Y-m-d 00:00:00', $cur_time));
			$today_end_time = strtotime(date('Y-m-d 23:59:59', $cur_time));
			$channel_oid_list_tmp = $this->get_ref_order_list(false, "channel_module='mall_order' AND user_id={$user_id} AND type_id=1 AND is_refund=0 AND {$today_begin_time}<=add_time AND add_time<={$today_end_time}", '', '0,99999999', 'channel_oid');
			foreach($channel_oid_list_tmp as $channel_oid_info_tmp)
			{
				$order_id_str .= $order_id_sp . $channel_oid_info_tmp['channel_oid']*1;
				$order_id_sp = ',';
			}
			if( strlen($order_id_str)>0 )
			{
				$mall_order_obj = POCO::singleton('pai_mall_order_class');
				
				//���⴦��
				if( in_array($seller_user_id_tmp, array(103511, 130968)) )
				{
					$today_tmp = $mall_order_obj->get_order_list($mall_type_id_tmp, -1, true, "order_id IN ({$order_id_str}) AND is_use_coupon=1 AND is_pay=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time} AND seller_user_id={$seller_user_id_tmp}");
					if( $today_tmp>=3 )
					{
						$result['result'] = -5;
						$result['message'] = '�������ÿ��ֻ��ʹ��3���Ż�ȯ';
						return $result;
					}
				}
				else
				{
					$today_tmp = $mall_order_obj->get_order_list($mall_type_id_tmp, -1, true, "order_id IN ({$order_id_str}) AND is_use_coupon=1 AND is_pay=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time}");
					if( $today_tmp>=1 )
					{
						$result['result'] = -5;
						$result['message'] = '�������ÿ��ֻ��ʹ��һ���Ż�ȯ';
						return $result;
					}
				}
			}
		}
		
		$result['result'] = 1;
		$result['message'] = '����';
		return $result;
	}
	
	/**
	 * ����ʹ�ý��
	 * ���ƣ��Żݽ��ҪС�ڶ������
	 * @param array $coupon_info
	 * @param array $param_info array( 'order_total_amount'=>0 )
	 * @return double ����2λС��
	 */
	public function cal_used_amount($coupon_info, $param_info)
	{
		//������
		$type_id = intval($coupon_info['type_id']);
		$face_value = number_format($coupon_info['face_value']*1, 2, '.', '')*1;
		$face_max = number_format($coupon_info['face_max']*1, 2, '.', '')*1;
		$order_total_amount = number_format($param_info['order_total_amount']*1, 2, '.', '')*1;
		if( $type_id<1 || $face_value<=0 || $order_total_amount<=0 )
		{
			return 0;
		}
		
		//����
		$used_amount = 0;
		if( $type_id==1 ) //�ֽ�ȯ
		{
			if( $face_value<$order_total_amount ) //���ƣ��Żݽ��ҪС�ڶ������
			{
				$used_amount = $face_value;
			}
		}
		elseif( $type_id==2 ) //�ۿ�ȯ
		{
			if( $face_value>0 && $face_value<100 && $face_max>0 ) //���ƣ��Żݱ���Ҫ��������Ҫ���޶�
			{
				$used_amount = $order_total_amount - round($order_total_amount*$face_value/100, 2);
				$used_amount = min($used_amount, $face_max);
				if( $used_amount>=$order_total_amount ) $used_amount = 0;
			}
		}
		elseif( $type_id==3 ) //�ļ�ȯ
		{
			if( $face_value<$order_total_amount && $face_max>0 ) //���ƣ�ָ�����ҪС�ڶ���������Ҫ���޶�
			{
				$used_amount = $order_total_amount - $face_value;
				$used_amount = min($used_amount, $face_max);
				if( $used_amount>=$order_total_amount ) $used_amount = 0;
			}
		}
		
		if( $used_amount<=0 )
		{
			return 0;
		}
		return number_format($used_amount, 2, '.', '')*1;
	}
	
	/**
	 * ����Ż�ȯ��Ч�ԡ����÷�Χ
	 * ����ʹ��ǰ�����Ż�ȯ���м��
	 * @param int $type_id
	 * @param string $coupon_sn
	 * @param array $param_info
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 * @tutorial
	 * 
	 * $param_info = array(
	 *  'channel_module'=>'yuepai', //��������
	 *  'channel_oid'=>0, //����ID
	 *  'module_type'=>'yuepai', //ģ������ waipai yuepai topic
	 *  'order_total_amount'=>'100.00', //�����ܽ��
	 *  'model_user_id'=>0, //ģ���û�ID��Լ�ġ�ר�⣩
	 *  'org_user_id'=>0, //�����û�ID
	 *  'event_id'=>'', //�ID
	 * );
	 * 
	 */
	public function check_coupon($user_id, $type_id, $coupon_sn, $param_info)
	{
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		$coupon_sn = trim($coupon_sn);
		if( !is_array($param_info) ) $param_info = array();
		$channel_module = trim($param_info['channel_module']);
		$channel_oid = intval($param_info['channel_oid']);
		$module_type = trim($param_info['module_type']);
		$order_total_amount = number_format($param_info['order_total_amount']*1, 2, '.', '')*1;
		if( $user_id<1 || $type_id<1 || strlen($coupon_sn)<1 || strlen($module_type)<1 || $order_total_amount<=0 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$cur_time = time();	//��ǰʱ�䣬����ͳһʹ�ô�ʱ��
		
		//��ȡ�Ż�ȯ��Ϣ
		$coupon_info = $this->get_coupon_info($coupon_sn);
		if( empty($coupon_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��ȯ������';
			return $result;
		}
		$type_id = intval($coupon_info['type_id']);
		/*
		if( $type_id!=$coupon_info['type_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '��ȯ���Ͳ���ȷ';
			return $result;
		}
		*/
		if( $coupon_info['is_give']==0 )
		{
			$result['result'] = -4;
			$result['message'] = '��ȯδ�һ�';
			return $result;
		}
		//���ȯ�ǲ��Ƿ��Ÿ����û���
		$ref_user_info = $this->get_ref_user_info_by_sn($coupon_sn);
		if( empty($ref_user_info) || $ref_user_info['user_id']!=$user_id )
		{
			$result['result'] = -5;
			$result['message'] = '��ȯ�ѱ��һ�';
			return $result;
		}
		//�������������Ƿ���ʹ���˴��Ż�ȯ
		$ref_order_info = $this->get_ref_order_info_by_sn($coupon_sn);
		if( (empty($ref_order_info) && $coupon_info['is_used']==1) || (!empty($ref_order_info) && $coupon_info['is_used']==0))
		{
			$result['result'] = -6;
			$result['message'] = '��ȯ��ʹ��';
			return $result;
		}
		if( !empty($ref_order_info) )
		{
			if($ref_order_info['channel_module']!=$channel_module || $ref_order_info['channel_oid']!=$channel_oid)
			{
				$result['result'] = -7;
				$result['message'] = '��ȯ��ʹ��';
				return $result;
			}
		}
		if( $coupon_info['is_cash']==1 )
		{
			$result['result'] = -8;
			$result['message'] = '��ȯ��ʹ��';
			return $result;
		}
		
		//��ȡ������Ϣ
		$batch_id = intval($coupon_info['batch_id']);
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -9;
			$result['message'] = '���β�����';
			return $result;
		}
		
		//����Ż�ȯ��Ч��
		$valid_ret = $this->check_coupon_valid($coupon_info, $cur_time, false);
		if( $valid_ret['result']!=1 )
		{
			$result['result'] = -10;
			$result['message'] = $valid_ret['message'];
			return $result;
		}
		
		//����Ż�ȯ��Ӧ��Χ
		$scope_ret = $this->check_coupon_scope($batch_info, $param_info, $cur_time);
		if( $scope_ret['result']!=1 )
		{
			$result['result'] = -11;
			$result['message'] = $scope_ret['message'];
			$result['message_white_false'] = $scope_ret['message_white_false'];
			$result['message_black_true'] = $scope_ret['message_black_true'];
			return $result;
		}
		
		//����ʹ�ý��
		$used_amount = $this->cal_used_amount($coupon_info, $param_info);
		if( $used_amount<=0 )
		{
			$result['result'] = -13;
			$result['message'] = '��ȯ������';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '��ȯ����';
		$result['used_amount'] = number_format($used_amount, 2, '.', '');
		return $result;
	}
	
	/**
	 * ʹ���Ż�ȯ
	 * ��ʱ���ƣ�һ������ֻ��ʹ��һ���Ż�ȯ
	 * �������ƣ�һ���Ż�ȯֻ������һ������
	 * @param int $user_id
	 * @param int $type_id
	 * @param string $coupon_sn
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @param array $param_info
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 * @tutorial
	 * 
	 * $param_info = array(
	 *  'module_type'=>'yuepai', //ģ������ waipai yuepai topic
	 *  'order_total_amount'=>'100.00', //�����ܽ��
	 *  'model_user_id'=>0, //ģ���û�ID��Լ�ġ�ר�⣩
	 *  'org_user_id'=>0, //�����û�ID
	 *  'event_id'=>'', //�ID
	 * );
	 * 
	 */
	public function use_coupon($user_id, $type_id, $coupon_sn, $channel_module, $channel_oid, $param_info)
	{
		//��־
		$log_arr = array(
			'func_get_args' => func_get_args(),
		);
		pai_log_class::add_log($log_arr, 'use_coupon', 'coupon');
		
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		//������
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		$coupon_sn = trim($coupon_sn);
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( !is_array($param_info) ) $param_info = array();
		$module_type = trim($param_info['module_type']);
		$order_total_amount = number_format($param_info['order_total_amount']*1, 2, '.', '')*1;
		if( $user_id<1 || $type_id<1 || strlen($coupon_sn)<1 || strlen($channel_module)<1 || $channel_oid<1 || strlen($module_type)<1 || $order_total_amount<=0 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$param_info['channel_module'] = $channel_module;
		$param_info['channel_oid'] = $channel_oid;
		$cur_time = time();	//��ǰʱ�䣬����ͳһʹ�ô�ʱ��
		
		//��ȡ�Ż�ȯ��Ϣ
		$coupon_info = $this->get_coupon_info($coupon_sn);
		if( empty($coupon_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��ȯ������';
			return $result;
		}
		$type_id = intval($coupon_info['type_id']);
		/*
		if( $type_id!=$coupon_info['type_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '��ȯ���Ͳ���ȷ';
			return $result;
		}
		*/
		if( $coupon_info['is_give']==0 )
		{
			$result['result'] = -4;
			$result['message'] = '��ȯδ�һ�';
			return $result;
		}
		//���ȯ�ǲ��Ƿ��Ÿ����û���
		$ref_user_info = $this->get_ref_user_info_by_sn($coupon_sn);
		if( empty($ref_user_info) || $ref_user_info['user_id']!=$user_id )
		{
			$result['result'] = -5;
			$result['message'] = '��ȯ�ѱ��һ�';
			return $result;
		}
		//�������������Ƿ���ʹ���˴��Ż�ȯ
		$ref_order_info = $this->get_ref_order_info_by_sn($coupon_sn);
		if( (empty($ref_order_info) && $coupon_info['is_used']==1) || (!empty($ref_order_info) && $coupon_info['is_used']==0))
		{
			$result['result'] = -6;
			$result['message'] = '��ȯ��ʹ��';
			return $result;
		}
		if( !empty($ref_order_info) || $coupon_info['is_used']==1 )
		{
			$result['result'] = -7;
			$result['message'] = '��ȯ��ʹ��';
			return $result;
		}
		if( $coupon_info['is_cash']==1 )
		{
			$result['result'] = -8;
			$result['message'] = '��ȯ��ʹ��';
			return $result;
		}
		
		//��ȡ������Ϣ
		$batch_id = intval($coupon_info['batch_id']);
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -9;
			$result['message'] = '���β�����';
			return $result;
		}
		$is_need_cash = intval($batch_info['is_need_cash']);
		$need_cash_rate = trim($batch_info['need_cash_rate']);
		$need_cash_max = trim($batch_info['need_cash_max']);
		
		//�жϴ��û��Ƿ����ʹ���Ż�ȯ����ֹˢ��
		$check_ret = $this->check_coupon_limit($user_id, $type_id, $param_info, $cur_time);
		if( $check_ret['result']!=1 )
		{
			$result['result'] = -10;
			$result['message'] = $check_ret['message'];
			return $result;
		}
		
		//����Ż�ȯ��Ч��
		$valid_ret = $this->check_coupon_valid($coupon_info, $cur_time);
		if( $valid_ret['result']!=1 )
		{
			$result['result'] = -10;
			$result['message'] = $valid_ret['message'];
			return $result;
		}
		
		//����Ż�ȯ��Ӧ��Χ
		$scope_ret = $this->check_coupon_scope($batch_info, $param_info, $cur_time);
		if( $scope_ret['result']!=1 )
		{
			$result['result'] = -11;
			$result['message'] = $scope_ret['message'];
			$result['message_white_false'] = $scope_ret['message_white_false'];
			$result['message_black_true'] = $scope_ret['message_black_true'];
			return $result;
		}
		
		//��ʱ���ƣ�һ������ֻ��ʹ��һ���Ż�ȯ
		$ref_order_list = $this->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( !empty($ref_order_list) )
		{
			$result['result'] = -12;
			$result['message'] = '�˵���ʹ����ȯ';
			return $result;
		}
		
		//����ʹ�ý��
		$used_amount = $this->cal_used_amount($coupon_info, $param_info);
		if( $used_amount<=0 )
		{
			$result['result'] = -13;
			$result['message'] = '��ȯ������';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//������ʹ��
		$ret = $this->update_coupon_used($coupon_sn, array('used_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -14;
			$result['message'] = 'ʹ��ʧ��';
			return $result;
		}
		
		//������ʹ��
		$ret = $this->update_ref_user_used($coupon_sn, array('used_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -15;
			$result['message'] = 'ʹ��ʧ��';
			return $result;
		}
		
		//�����������
		$ref_data = array(
			'channel_module' => $channel_module,
			'channel_oid' => $channel_oid,
			'user_id' => $user_id,
			'coupon_sn' => $coupon_sn,
			'batch_id' => $batch_id,
			'is_need_cash' => $is_need_cash,
			'need_cash_rate' => $need_cash_rate,
			'need_cash_max' => $need_cash_max,
			'type_id' => $type_id,
			'face_value' => $coupon_info['face_value'],
			'face_max' => $coupon_info['face_max'],
			'used_amount' => $used_amount,
			'add_time' => $cur_time,
		);
		$id = $this->add_ref_order($ref_data);
		if( $id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -16;
			$result['message'] = 'ʹ��ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['used_amount'] = number_format($used_amount, 2, '.', '');
		return $result;
	}
	
	/**
	 * ��ʹ���Ż�ȯ
	 * ���ô˷���֮ǰ����Ҫ��ȷ������δ֧��
	 * ��������֧���������refund_coupon
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 */
	public function not_use_coupon_by_oid($channel_module, $channel_oid)
	{
		//��־
		$log_arr = array(
			'func_get_args' => func_get_args(),
		);
		pai_log_class::add_log($log_arr, 'not_use_coupon_by_oid', 'coupon');
		
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		//������
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_list = $this->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( empty($ref_order_list) )
		{
			$result['result'] = 1;
			$result['message'] = '�˵�û��ʹ��ȯ';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//ѭ������
		$used_amount = 0;
		foreach ($ref_order_list as $ref_order_info)
		{
			$not_use_ret = $this->not_use_coupon($ref_order_info['id']);
			if( $not_use_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -3;
				$result['message'] = $not_use_ret['message'];
				return $result;
			}
			$used_amount += ($not_use_ret['used_amount']*1);
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['used_amount'] = number_format($used_amount, 2, '.', '');
		return $result;
	}
	
	/**
	 * ��ʹ���Ż�ȯ
	 * ���ô˷���֮ǰ����Ҫ��ȷ������δ֧��
	 * ��������֧���������refund_coupon
	 * @param int $id
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 */
	public function not_use_coupon($id)
	{
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		//������
		$id = intval($id);
		if( $id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '������';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -3;
			$result['message'] = '�޷���ʹ��ȯ';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����δʹ��
		$ret = $this->update_coupon_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//����δʹ��
		$ret = $this->update_ref_user_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//��ȡ��������
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//ɾ����������
		$ret = $this->del_ref_order($id);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -7;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['used_amount'] = $ref_order_info['used_amount'];
		return $result;
	}
	
	/**
	 * �˻��Ż�ȯ
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_coupon_by_oid($channel_module, $channel_oid)
	{
		//��־
		$log_arr = array(
			'func_get_args' => func_get_args(),
		);
		pai_log_class::add_log($log_arr, 'refund_coupon_by_oid', 'coupon');
		
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_list = $this->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( empty($ref_order_list) )
		{
			$result['result'] = 1;
			$result['message'] = '�˵�û��ʹ��ȯ';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//ѭ������
		foreach ($ref_order_list as $ref_order_info)
		{
			$refund_ret = $this->refund_coupon($ref_order_info['id']);
			if( $refund_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -3;
				$result['message'] = $refund_ret['message'];
				return $result;
			}
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * �˻��Ż�ȯ
	 * @param int $id
	 * @return array
	 */
	public function refund_coupon($id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$id = intval($id);
		if( $id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '������';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -3;
			$result['message'] = 'ȯ�޷��˻�';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		$cur_time = time(); 
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����δʹ��
		$ret = $this->update_coupon_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//����δʹ��
		$ret = $this->update_ref_user_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�������˻�
		$ret = $this->update_ref_order_refund($id, array('refund_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * �����Ż�ȯ
	 * @param int $id
	 * @param double $cash_amount
	 * @param array $more_info array('event_id'=>0, 'in_user_id'=>0, 'org_user_id'=>0, 'need_amount'=>0.00, 'org_amount'=>0.00, 'subject'=>'')
	 * @return array
	 */
	public function cash_coupon($id, $cash_amount, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$id = intval($id);
		$cash_amount = number_format($cash_amount*1, 2, '.', '')*1;
		$event_id = intval($more_info['event_id']);
		$in_user_id = intval($more_info['in_user_id']);
		$org_user_id = intval($more_info['org_user_id']);
		$need_amount = number_format($more_info['need_amount']*1, 2, '.', '')*1;
		$org_amount = number_format($more_info['org_amount']*1, 2, '.', '')*1;
		$in_amount = bcsub($cash_amount, $org_amount, 2);	//�����߶��ֽ����ֽ�� ��ȥ �������ֽ��
		$subject = trim($more_info['subject']);
		if( $id<1 || $cash_amount<0 || $need_amount<0 || $org_amount<0 || $in_amount<0 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '������';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -3;
			$result['message'] = 'ȯ�޷�����';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		$cur_time = time();
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�����Ѷ���
		$ret = $this->update_coupon_cash($coupon_sn, array('cash_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����Ѷ���
		$data = array(
			'subject' => $subject,
			'event_id' => $event_id,
			'in_user_id' => $in_user_id,
			'org_user_id' => $org_user_id,
			'need_amount' => $need_amount,
			'in_amount' => $in_amount,
			'org_amount' => $org_amount,
			'cash_time' => $cur_time,
		);
		$ret = $this->update_ref_order_cash($id, $cash_amount, $data);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * �����Ż�ȯ
	 * @param int $id
	 * @return array
	 */
	public function settle_coupon($id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$id = intval($id);
		if( $id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '������';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==0 )
		{
			$result['result'] = -3;
			$result['message'] = 'ȯ�޷�����';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		$cur_time = time();
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�����ѽ���
		$ret = $this->update_coupon_settle($coupon_sn, array('settle_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
				
			$result['result'] = -4;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����ѽ���
		$ret = $this->update_ref_order_settle($id, array('settle_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * ��ȡ�Ż�ȯ����
	 * @param string $coupon_sn
	 * @param int $user_id ����Ż�ȯ�Ƿ����ڴ��û�
	 * @return array
	 */
	public function get_coupon_detail_by_sn($coupon_sn, $user_id=0)
	{
		$coupon_sn = trim($coupon_sn);
		$user_id = intval($user_id);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		
		//����Ż�ȯ�Ƿ����ڴ��û�
		if( $user_id>0 )
		{
			$ref_user_info = $this->get_ref_user_info_by_sn($coupon_sn);
			if( empty($ref_user_info) || $user_id!=$ref_user_info['user_id'])
			{
				return array();
			}
		}
		
		//��ȡ�Ż�ȯ��Ϣ
		$detail_list = $this->fill_coupon_detail_list(array(array('coupon_sn'=>$coupon_sn)));
		if( empty($detail_list) )
		{
			return array();
		}
		$coupon_info = $detail_list[0];
		if( !is_array($coupon_info) ) $coupon_info = array();
		return $coupon_info;
	}
	
	/**
	 * ��ȡ�û����Ż�ȯ�б��ҵ��Ż�ȯҳ��
	 * @param string $tab ��ʹ��available ��ʹ��used �ѹ���expired
	 * @param int $user_id
	 * @param boolean $b_select_count
	 * @param string $order_by
	 * @param string $limit
	 * @return array|int
	 */
	public function get_user_coupon_list_by_tab($tab, $user_id, $b_select_count=false, $order_by='coupon_id ASC', $limit='0,20')
	{
		$tab = trim($tab);
		$user_id = intval($user_id);
		if( !in_array($tab, array('available', 'used', 'expired')) || $user_id<1 )
		{
			return $b_select_count?0:array();
		}
		
		$cur_time = time();
		
		//��������
		$where_str = '';
		if( $tab=='available' )
		{
			$where_str = "is_used=0 AND {$cur_time}<=end_time";
		}
		elseif( $tab=='used' )
		{
			$where_str = "is_used=1";
		}
		elseif( $tab=='expired' )
		{
			$where_str = "is_used=0 AND end_time<{$cur_time}";
		}
		
		//��ȡ�����û��б�
		$ref_user_list = $this->get_ref_user_list($user_id, 0, $b_select_count, $where_str, 'id ASC', '0,99999999', 'coupon_sn');
		if( $b_select_count )
		{
			return $ref_user_list;
		}
		
		//��ѯ�Ż�ȯ�б�
		$coupon_list = $this->fill_coupon_detail_list($ref_user_list, $order_by, $limit);
		return $coupon_list;
	}
	
	/**
	 * ��ȡ�û��Ŀ����Ż�ȯ������֧��ҳ��
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @param boolean $b_select_count
	 * @param string $order_by
	 * @param bool $limit_coupon �Ƿ������Ż�ȯ
	 * @param string $limit_message �����Ż�ȯ����ʾ����
	 * @return array|int
	 * @tutorial
	 * 
	 * $param_info = array(
	 *  'channel_module'=>'yuepai', //��������
	 *  'channel_oid'=>0, //����ID
	 *  'module_type'=>'yuepai', //ģ������ waipai yuepai topic
	 *  'order_total_amount'=>'100.00', //�����ܽ��
	 *  'model_user_id'=>0, //ģ���û�ID��Լ�ġ�ר�⣩
	 *  'org_user_id'=>0, //�����û�ID
	 *  'event_id'=>'', //�ID
	 * );
	 * 
	 */
	public function get_user_coupon_list_by_check($user_id, $type_id, $param_info, $b_select_count=false, $order_by='coupon_id ASC', &$limit_coupon=false, &$limit_message='')
	{
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		if( $user_id<1 || $type_id<1 )
		{
			return $b_select_count?0:array();
		}
		
		$cur_time = time();
		
		//�жϴ��û��Ƿ����ʹ���Ż�ȯ����ֹˢ��
		$limit_coupon = false;
		$limit_message = '';
		$check_ret = $this->check_coupon_limit($user_id, $type_id, $param_info, $cur_time);
		if( $check_ret['result']!=1 )
		{
			$limit_coupon = true;
			$limit_message = trim($check_ret['message']);
			return $b_select_count?0:array();
		}
		
		//��ȡ�˵���ռ�õ��Ż�ȯ
		$coupon_sn_arr = array();
		$coupon_sn_str = '';
		$coupon_sn_sp = '';
		$ref_order_list = $this->get_ref_order_list_by_oid($param_info['channel_module'], $param_info['channel_oid']);
		foreach ($ref_order_list as $ref_order_info)
		{
			$coupon_sn_arr[] = trim($ref_order_info['coupon_sn']);
			$coupon_sn_str .= $coupon_sn_sp . "'" . mysql_escape_string($ref_order_info['coupon_sn']) . "'";
			$coupon_sn_sp = ',';
		}
		
		//��ȡ�����û��б�
		$where_str = "start_time<={$cur_time} AND {$cur_time}<=end_time";
		if( strlen($coupon_sn_str)>0 )
		{
			$where_str .= " AND (is_used=0 OR coupon_sn IN ({$coupon_sn_str}))";
		}
		else
		{
			$where_str .= " AND is_used=0";
		}
		$ref_user_list = $this->get_ref_user_list($user_id, 0, false, $where_str, 'id ASC', '0,99999999', 'coupon_sn');
		
		//��ѯ�Ż�ȯ�б�
		$coupon_list = $this->fill_coupon_detail_list($ref_user_list, $order_by, '0,99999999');
		
		//�������Ż�ȯ
		$result_list = array();
		$result_count = 0;
		foreach ($coupon_list as $coupon_info)
		{
			//����Ƿ�����
			$check_ret = $this->check_coupon($user_id, $type_id, $coupon_info['coupon_sn'], $param_info);
			if( $check_ret['result']!=1 )
			{
				continue;
			}
			
			//�����Ż�ȯʹ��״̬
			$coupon_sn_tmp = trim($coupon_info['coupon_sn']);
			if( in_array($coupon_sn_tmp, $coupon_sn_arr, true) )
			{
				$coupon_info['is_used'] = 0;
				$coupon_info['tab'] = 'available';
				$coupon_info['tab_str'] = '��ʹ��';
			}
			
			$result_list[] = $coupon_info;
			$result_count++;
		}
		if($b_select_count)
		{
			return $result_count;
		}
		$result_list = array_slice($result_list, 0, 100); //�����100�ţ�������̫�࣬ǰ��ҳ��Ῠס
		return $result_list;
	}
	
	/**
	 * ����Ż��б�
	 * @param array $list
	 * @param string $order_by
	 * @param string $limit
	 * @param bool $b_detail
	 * @return array
	 */
	private function fill_coupon_detail_list($list, $order_by='coupon_id ASC', $limit='0,20', $b_detail=true)
	{
		if( !is_array($list) || empty($list) )
		{
			return array();
		}
		//����
		$coupon_sn_str = '';
		$coupon_sn_sp = '';
		foreach ($list as $info)
		{
			$coupon_sn_tmp = trim($info['coupon_sn']);
			if( strlen($coupon_sn_tmp)>1 )
			{
				$tmp = ':x_coupon_sn';
				sqlSetParam($tmp, "x_coupon_sn", $coupon_sn_tmp);
				$coupon_sn_str .= $coupon_sn_sp . $tmp;
				$coupon_sn_sp = ',';
			}
		}
		if( strlen($coupon_sn_str)<1 )
		{
			return array();
		}
		$coupon_list = $this->get_coupon_list(0, false, "coupon_sn IN ({$coupon_sn_str})", $order_by, $limit);
		
		//��ȡ������Ϣ
		if( $b_detail )
		{
			$cur_time = time();
			
			foreach ($coupon_list as $coupon_key=>$coupon_info)
			{
				$batch_id = intval($coupon_info['batch_id']);
				$batch_info = $this->get_batch_info($batch_id);
				if( empty($batch_info) )
				{
					continue;
				}
				
				$start_time = intval($coupon_info['start_time']);
				$start_time_str = date('Y.m.d', $start_time);
				$coupon_info['start_time_str'] = $start_time_str;
				
				$end_time = intval($coupon_info['end_time']);
				$end_time_str = date('Y.m.d', $end_time);
				$coupon_info['end_time_str'] = $end_time_str;

				$used_time = intval($coupon_info['used_time']);
				$used_time_str 	= date('Y.m.d', $used_time);
				$coupon_info['used_time_str'] = $used_time_str;				

				if( $coupon_info['is_used']==0 )
				{
					if( $cur_time<=$end_time )
					{
						$coupon_info['tab'] = 'available';
						$coupon_info['tab_str'] = '��ʹ��';
					}
					else
					{
						$coupon_info['tab'] = 'expired';
						$coupon_info['tab_str'] = '�ѹ���';
					}
				}
				else
				{
					$coupon_info['tab'] = 'used';
					$coupon_info['tab_str'] = '��ʹ��';
				}
				
				$coupon_info['batch_name'] = trim($batch_info['batch_name']);
				$coupon_info['batch_desc'] = trim($batch_info['batch_desc']);
				$coupon_info['scope_module_type'] = trim($batch_info['scope_module_type']);
				$coupon_info['scope_module_type_name'] = trim($batch_info['scope_module_type_name']);
				$coupon_info['scope_order_total_amount'] = trim($batch_info['scope_order_total_amount']);
				
				$coupon_list[$coupon_key] = $coupon_info;
			}
		}
		
		return $coupon_list;
	}

	/**
	 * ��ȡ��ȡ����
	 * @param int $supply_id
	 * @return array
	 */
	public function get_supply_detail($supply_id)
	{
		$supply_id = intval($supply_id);
		if( $supply_id<1 )
		{
			return array();
		}
		
		//��ȡ��ȡ��Ϣ
		$supply_info = $this->get_supply_info($supply_id);
		if( empty($supply_info) )
		{
			return array();
		}
		
		//��ȡ������Ϣ
		$batch_id = intval($supply_info['batch_id']);
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			return array();
		}
		
		//������Ż�ȯ�ֶΣ�����ҳ��ʹ��
		$supply_info['batch_name'] = trim($batch_info['batch_name']);
		$supply_info['batch_desc'] = trim($batch_info['batch_desc']);
		$supply_info['type_id'] = trim($batch_info['coupon_type_id']);
		$supply_info['face_value'] = trim($batch_info['coupon_face_value']);
		$supply_info['start_time'] = trim($batch_info['coupon_start_time']);
		$supply_info['end_time'] = trim($batch_info['coupon_end_time']);
		
		$start_time = intval($supply_info['start_time']);
		$start_time_str = date('Y.m.d', $start_time);
		$supply_info['start_time_str'] = $start_time_str;
		
		$end_time = intval($supply_info['end_time']);
		$end_time_str = date('Y.m.d', $end_time);
		$supply_info['end_time_str'] = $end_time_str;
		
		$supply_info['scope_module_type'] = trim($batch_info['scope_module_type']);
		$supply_info['scope_module_type_name'] = trim($batch_info['scope_module_type_name']);;
		$supply_info['scope_order_total_amount'] = trim($batch_info['scope_order_total_amount']);
		
		return $supply_info;
	}
	
	/**
	 * ��ȡȯ
	 * @param int $supply_id
	 * @param int $user_id
	 * @return array
	 */
	public function give_supply_coupon($supply_id, $user_id)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$supply_id = intval($supply_id);
		$user_id = intval($user_id);
		if( $supply_id<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		$cur_time = time();
		
		//��ȡ��ȡ��Ϣ
		$supply_info = $this->get_supply_info($supply_id);
		if( empty($supply_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��������';
			return $result;
		}
		$supply_begin_time = intval($supply_info['supply_begin_time']);
		$supply_end_time = intval($supply_info['supply_end_time']);
		$supply_status = intval($supply_info['supply_status']);
		if($cur_time<$supply_begin_time)
		{
			$result['result'] = -3;
			$result['message'] = 'δ��ʼ';
			return $result;
		}
		if($supply_end_time<$cur_time)
		{
			$result['result'] = -4;
			$result['message'] = '�ѽ���';
			return $result;
		}
		if( $supply_status!=1 )
		{
			$result['result'] = -5;
			$result['message'] = '����ͣ';
			return $result;
		}
		
		//����Ƿ�����ȡ
		$supply_user_info = $this->get_supply_user_info($supply_id, $user_id);
		if( empty($supply_user_info) )
		{
			$result['result'] = -6;
			$result['message'] = '�ѽ���';
			return $result;
		}
		if( $supply_user_info['is_give']==1 )
		{
			$result['result'] = -7;
			$result['message'] = '����ȡ';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		$batch_id = intval($supply_info['batch_id']);
		$ret = $this->give_coupon_by_create($user_id, $batch_id);
		if( $ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -8;
			$result['message'] = '������';
			return $result;
		}
		$coupon_sn = trim($ret['coupon_sn']);
		
		$id = intval($supply_user_info['id']);
		$more_info = array('coupon_sn'=>$coupon_sn, 'give_time'=>$cur_time);
		$ret = $this->update_supply_user_give($id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -9;
			$result['message'] = '��ȡ����';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * ��ȡ�û�����ÿ����Ż�ȯ��֧��ҳ��Ĭ��ѡ���Ż�ȯ
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @return array
	 */
	public function get_user_coupon_info_best($user_id, $type_id, $param_info)
	{
		$list = $this->get_user_coupon_list_by_check($user_id, $type_id, $param_info, false, 'face_value DESC,end_time ASC,coupon_id DESC');
		if( empty($list) || !is_array($list[0]) || empty($list[0]) )
		{
			return array();
		}
		return $list[0];
	}
	
	/**
	 * ��ȡ�û��Ŀ����Ż�ȯ������Ԫ��Լ�ļ۸����
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @return double
	 * @tutorial
	 *
	 * $param_info = array(
	 *  'channel_module'=>'yuepai', //��������
	 *  'channel_oid'=>0, //����ID
	 *  'module_type'=>'yuepai', //ģ������ waipai yuepai
	 *  'order_total_amount'=>'100.00', //�����ܽ��
	 *  'model_user_id'=>0, //ģ���û�ID��Լ�ġ�ר�⣩
	 *  'org_user_id'=>0, //�����û�ID
	 *  'event_id'=>'', //�ID
	 * );
	 * 
	 */
	public function get_user_coupon_total_face_value($user_id, $type_id, $param_info)
	{
		$total_face_value = 0;
		$list = $this->get_user_coupon_list_by_check($user_id, $type_id, $param_info, false, 'coupon_id ASC');
		foreach ($list as $info)
		{
			$total_face_value = $total_face_value + $info['face_value']*1;
		}
		return $total_face_value;
	}
	
	/**
	 * ��ȡ����ͳ��
	 * @param int $begin_time
	 * @param int $end_time
	 * @param int $batch_id
	 * @return array
	 */
	public function get_stat_give_list($begin_time, $end_time, $batch_id=0)
	{
		$begin_time = intval($begin_time);
		$end_time = intval($end_time);
		$batch_id = intval($batch_id);
		if( $begin_time<1 || $end_time<1 || $begin_time>$end_time )
		{
			return array();
		}
		
		$where_str = "add_time>={$begin_time} AND add_time<={$end_time}";
		if( $batch_id>0 )
		{
			$where_str .= " AND batch_id={$batch_id}";
		}
		
		$this->set_coupon_ref_user_tbl();
		$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') AS add_date,batch_id,count(batch_id) AS quantity FROM {$this->_db_name}.{$this->_tbl_name}";
		$sql .= " WHERE {$where_str} GROUP BY add_date,batch_id ORDER BY add_date DESC,batch_id ASC";
		return $this->findBySql($sql);
	}
	
	/**
	 * ��ȡ����ͳ��
	 * @param string $channel_module
	 * @param int $begin_time
	 * @param int $end_time
	 * @return array
	 */
	public function get_stat_cash_list($channel_module, $begin_time, $end_time)
	{
		$channel_module = trim($channel_module);
		$begin_time = intval($begin_time);
		$end_time = intval($end_time);
		if( $begin_time<1 || $end_time<1 || $begin_time>$end_time )
		{
			return array();
		}
		
		$where_str = "is_cash=1 AND cash_time>={$begin_time} AND cash_time<={$end_time}";
		if( strlen($channel_module)>0 )
		{
			if( strlen($where_str)>0 ) $where_str .= ' AND ';
			$where_str .= "channel_module=:x_channel_module";
			sqlSetParam($where_str, 'x_channel_module', $channel_module);
		}
		
		$this->set_coupon_ref_order_tbl();
		$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(cash_time),'%Y-%m-%d') AS cash_date,COUNT(DISTINCT user_id) AS uv,COUNT(id) AS pv,SUM(used_amount) AS used_amount,SUM(cash_amount) AS cash_amount FROM {$this->_db_name}.{$this->_tbl_name}";
		$sql .= " WHERE {$where_str} GROUP BY cash_date ORDER BY cash_date DESC";
		$list = $this->findBySql($sql);
		
		return $list;
	}
	
	/**
	 * ��ȡ�Ż�ȯ�˻�
	 * @return array
	 */
	public function get_coupon_account_info()
	{
		$payment_obj = POCO::singleton('pai_payment_class');
		$coupon_user_id = $payment_obj->get_coupon_user_id();
		return $payment_obj->get_user_account_info($coupon_user_id);
	}
	
	/**
	 * ��ȡ�Ż�ȯ�˻�ͳ��
	 * @param int $begin_time
	 * @param int $end_time
	 * @return array
	 */
	public function get_stat_coupon_list($begin_time, $end_time)
	{
		$payment_obj = POCO::singleton('pai_payment_class');
		$coupon_user_id = $payment_obj->get_coupon_user_id();
		$account_info = $payment_obj->get_user_account_info($coupon_user_id);
		return $payment_obj->get_stat_account_list('actual', $account_info['account_id'], $begin_time, $end_time);
	}
	
}
