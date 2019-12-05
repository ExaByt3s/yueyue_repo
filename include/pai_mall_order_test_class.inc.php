<?php
/**
 * ͨ�ö�����
 * 
 * @author
 */

class pai_mall_order_test_class extends POCO_TDG
{
	/**
	 * ����ģ��
	 * @var string
	 */
	private $channel_module = 'mall_order';

	/**
	 * ��֧��
	 * ������µ����ȴ����֧��
	 * @var int
	 */
	const STATUS_WAIT_PAY = 0;
	
	/**
	 * ��ȷ��
	 * �����֧�����ȴ�����ȷ��
	 * @var int
	 */
	const STATUS_WAIT_CONFIRM = 1;
	
	/**
	 * ��ǩ��
	 * ������ȷ�ϣ��ȴ����ǩ��
	 * @var int
	 */
	const STATUS_WAIT_SIGN = 2;
	
	/**
	 * �ѹر�
	 * @var int
	 */
	const STATUS_CLOSED = 7;
	
	/**
	 * �����
	 * @var int
	 */
	const STATUS_SUCCESS = 8;

	/**
     * ���������������˿�ʱ�䣨Сʱ��
     * @var int
	 */
	const ALLOW_BUYER_REFUND_TIME = 12;

	/**
	 * ״̬����
	 * @var array
	 */
	private $status_str_arr = array(
		0 => '��֧��',
		1 => '��ȷ��',
		2 => '��ǩ��',
		7 => '�ѹر�',
		8 => '�����',
	);

	/**
	 * �������񶩵�����
	 * @var null|object
	 */
	private $order_detail_obj = NULL;

	/**
	 * �������������
	 * @var null|object
	 */
	private $order_activity_obj = NULL;
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
		$this->order_detail_obj = POCO::singleton('pai_mall_order_detail_class');
		$this->order_activity_obj = POCO::singleton('pai_mall_order_activity_class');
	}
	
	/**
	 * ָ����
	 */
	private function set_mall_order_tbl()
	{
		$this->setTableName('mall_order_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_mall_order_process_tbl()
	{
		$this->setTableName('mall_order_process_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_mall_order_code_tbl()
	{
		$this->setTableName('mall_order_code_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_mall_comment_buyer_tbl()
	{
		$this->setTableName('mall_comment_buyer_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_mall_comment_seller_tbl()
	{
		$this->setTableName('mall_comment_seller_tbl');
	}

	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_order($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $order_id
	 * @return boolean
	 */
	private function update_order($data, $order_id)
	{
		$order_id = intval($order_id);
		if( !is_array($data) || empty($data) || $order_id<1 )
		{
			return false;
		}
		$this->set_mall_order_tbl();
		$affected_rows = $this->update($data, "order_id={$order_id}");
		return $affected_rows>0 ? true : false;
	}
	
	/**
	 * �޸Ķ�������Ϣ
	 * @param array $data
	 * @param string $where_str
	 * @return boolean
	 */
	private function update_order_by_where($data, $where_str)
	{
		$where_str = trim($where_str);
		if( !is_array($data) || empty($data) || strlen($where_str)<1 )
		{
			return false;
		}
		$this->set_mall_order_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0 ? true : false;
	}

	/**
	 * ��ȡ��Ϣ
	 * @param int $order_id
	 * @return array
	 */
	public function get_order_info_by_id($order_id)
	{
		$order_id = intval($order_id);
		if( $order_id<1 )
		{
			return array();
		}
		$this->set_mall_order_tbl();
		return $this->find("order_id={$order_id}");
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param string $order_sn
	 * @return array
	 */
	public function get_order_info($order_sn)
	{
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			return array();
		}
		$where_str = 'order_sn=:x_order_sn';
		sqlSetParam($where_str, 'x_order_sn', $order_sn);
		$this->set_mall_order_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ������Ϣ
	 * @param string $order_sn
	 * @return array
	 */
	public function get_order_full_info($order_sn, $login_user_id=0)
	{
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			return array();
		}
		$login_user_id = intval($login_user_id);
		$order_full_list = $this->fill_order_full_list(array($order_info), $login_user_id);
		$order_full_info = $order_full_list[0];
		if( !is_array($order_full_info) ) $order_full_info = array();
		return $order_full_info;
	}
	
	/**
	 * ͨ������id��ȡ������Ϣ
	 * @param string $order_sn
	 * @return array
	 */
	public function get_order_full_info_by_id($order_id, $login_user_id=0)
	{
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			return array();
		}
		$login_user_id = intval($login_user_id);
		$order_full_list = $this->fill_order_full_list(array($order_info), $login_user_id);
		$order_full_info = $order_full_list[0];
		if( !is_array($order_full_info) ) $order_full_info = array();
		return $order_full_info;
	}

	//TODO
	public function get_order_list_for_activity($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{

	}

	/**
	 * ��ȡ�б�
	 * @param int $type_id ��ƷƷ��id
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_order_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$type_id = intval($type_id);
		$status = intval($status);
		
		//�����ѯ����
		$sql_where = '';
		if( $type_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "type_id={$type_id}";
		}
		if( $status>=0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "status={$status}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_mall_order_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
        
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}


	/**
	 * ��������Ӳ�ѯ�����Ķ����б�
	 * @param int $type_id ��ƷƷ��ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string @where_str ��ѯ���
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @return array
	 */
	public function get_order_full_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$ret = $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
	}

	/**
	 * ͨ��goods_id��ȡ�����б�
	 * @param  array   $goods_ids      ��Ʒid�б�
	 * @param  int     $status         ����״̬
	 * @param  boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param  string  $order_by       ����
	 * @param  string  $limit          ��ѯ����
	 * @param  string  $fields         ��ѯ�ֶ�
	 * @return array
	 */
	public function get_order_list_by_goods_ids($type_id, $status, $goods_ids, $b_select_count=false, $where_str, $order_by='d.order_id', $limit='0,20', $fields='o.*')
	{
		$rst = $this->order_detail_obj->get_order_list_by_goods_ids($type_id, $status, $goods_ids, $b_select_count, $where_str, $order_by, $limit, $fields);
	    return $this->fill_order_full_list($rst);
	}
	
	/**
	 * ��ȡ��Ҷ����б�
	 * @param int $user_id ����û�ID
	 * @param int $type_id ��ƷƷ��ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_buyer_comment [����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//�����ѯ����
		$where_str = "buyer_user_id={$user_id} AND is_buyer_del=0";
		$sql_where = '';
		if( $is_buyer_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_buyer_comment={$is_buyer_comment}";
		}
		if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
		$sql_where .= $where_str;

		$ret = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
	}

	/**
	 * ��ȡ��Ҷ����б���ǿ�棩
	 * @param int $user_id ����û�ID
	 * @param int $type_id ��ƷƷ��ID
	 * @param array $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $where_str ��ѯ���
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_fill_order �Ƿ���Ҫ������ϸ��Ϣ��detail,process�ȣ�
	 * @param int $is_buyer_comment [����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_buyer_comment=-1)
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_buyer_comment = intval($is_buyer_comment);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//�����ѯ����
		$sql_where = " buyer_user_id={$user_id} AND is_buyer_del=0 ";
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		if( $is_buyer_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_buyer_comment={$is_buyer_comment}";
		}

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $rst;
		}
		if( $is_fill_order==1 )
		{
			$rst = $this->fill_order_full_list($rst);
		}
		return $rst;
	}
	
	/**
	 * ��ȡ���Ҷ����б�
	 * @param int $user_id �����û�ID
	 * @param int $type_id ��ƷƷ��ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_seller_comment [�����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_seller_comment=-1)
	{
		$user_id = intval($user_id);
		$is_seller_comment = intval($is_seller_comment);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//�����ѯ����
		$where_str = "seller_user_id={$user_id} AND is_seller_del=0";
		$sql_where = '';
		if( $is_seller_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_seller_comment={$is_seller_comment}";
		}
		if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
		$sql_where .= $where_str;

		$ret = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
	}

	/**
	 * ��ȡ���Ҷ����б���ǿ�棩
	 * @param int $user_id ����û�ID
	 * @param int $type_id ��ƷƷ��ID
	 * @param array $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $where_str ��ѯ���
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_fill_order �Ƿ���Ҫ������ϸ��Ϣ��detail,process�ȣ�
	 * @param int $is_buyer_comment [�����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_seller_comment=-1)
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_seller_comment = intval($is_seller_comment);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//�����ѯ����
		$sql_where = "seller_user_id={$user_id} AND is_seller_del=0 ";
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		if( $is_seller_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_seller_comment={$is_seller_comment}";
		}

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $rst;
		}
		if( $is_fill_order==1 )
		{
			$rst = $this->fill_order_full_list($rst);
		}
		return $rst;
	}

	/**
	 * ��ȡ��Ҷ���������Ŀ����
	 * @param int $user_id ����û�ID
	 * @return array
	 */
	public function get_order_number_for_buyer($user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$fields = ' status,COUNT(*) as c ';
		$where_str = " buyer_user_id={$user_id} AND is_seller_del=0 GROUP BY status ";
		$ret = $this->get_order_list(0, -1, false, $where_str, '', '0,99999999', $fields);

		//��ȡ������״̬
		$comment_where_str = " buyer_user_id={$user_id} AND is_seller_del=0 AND is_buyer_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

		$ret_tmp = array();
		foreach ( $ret as $key => $info )
		{
			switch ( $info['status'] )
			{
				case 0:
					$key = 'wait_pay';
					break;
				case 1:
					$key = 'wait_confirm';
					break;
				case 2:
					$key = 'wait_sign';
					break;
				case 7:
					$key = 'closed';
					break;
				case 8:
					$key = 'success';
					break;
				default:
					// $key = 'all';
					break;
			}
			$ret_tmp[$key] = $info['c'];
		}

		$count = intval($ret_tmp['wait_pay'])+intval($ret_tmp['wait_sign'])+intval($ret_tmp['wait_confirm']);

		$res = array(
			'wait_pay' => isset($ret_tmp['wait_pay']) ? $ret_tmp['wait_pay'] : 0,
			'wait_confirm' => isset($ret_tmp['wait_confirm']) ? $ret_tmp['wait_confirm'] : 0,
			'wait_sign' => isset($ret_tmp['wait_sign']) ? $ret_tmp['wait_sign'] : 0,
			'wait_comment' => intval($comment_ret),
			'all' => $count,
		);

		return $res;
	}

	/**
	 * ��ȡ���Ҷ���������Ŀ����
	 * @param int $user_id �����û�ID
	 * @return array
	 */
	public function get_order_number_for_seller($user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$fields = ' status,COUNT(*) as c ';
		$where_str = " seller_user_id={$user_id} AND is_seller_del=0 GROUP BY status ";
		$ret = $this->get_order_list(0, -1, false, $where_str, '', '0,99999999', $fields);

		//��ȡ������״̬
		$comment_where_str = " seller_user_id={$user_id} AND is_seller_del=0 AND is_seller_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

		$ret_tmp = array();
		foreach ( $ret as $key => $info )
		{
			switch ( $info['status'] )
			{
				case 0:
					$key = 'wait_pay';
					break;
				case 1:
					$key = 'wait_confirm';
					break;
				case 2:
					$key = 'wait_sign';
					break;
				case 7:
					$key = 'closed';
					break;
				case 8:
					$key = 'success';
					break;
				default:
					// $key = 'all';
					break;
			}
			$ret_tmp[$key] = $info['c'];
		}

		$count = intval($ret_tmp['wait_pay'])+intval($ret_tmp['wait_sign'])+intval($ret_tmp['wait_confirm'])+intval($comment_ret);

		$res = array(
			'wait_pay' => isset($ret_tmp['wait_pay']) ? $ret_tmp['wait_pay'] : 0,
			'wait_confirm' => isset($ret_tmp['wait_confirm']) ? $ret_tmp['wait_confirm'] : 0,
			'wait_sign' => isset($ret_tmp['wait_sign']) ? $ret_tmp['wait_sign'] : 0,
			'wait_comment' => intval($comment_ret),
			'all' => $count,
		);

		return $res;
	}

	/**
	 * ���䶩��������Ϣ
	 * @param array $list
	 * @param int $login_user_id ��ǰ��¼��id
	 * @return array
	 */
	private function fill_order_full_list($list, $login_user_id=0)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		$login_user_id = intval($login_user_id);
		$cur_time = time();

		//��ȡϵͳ�ǳơ�ϵͳͷ��
		$sys_nickname = get_user_nickname_by_user_id(10002);
		$sys_icon = get_user_icon(10002, 165);
		
		foreach($list as $key=>$info)
		{
			//����״̬
			$status_tmp = intval($info['status']);
			if( array_key_exists($status_tmp, $this->status_str_arr) )
			{
				$status_str_tmp = $this->status_str_arr[$status_tmp];
			}
			else
			{
				$status_str_tmp ='δ֪״̬';
			}
			$info['status_str'] = $status_str_tmp;

			$info['status_str2'] = $status_str_tmp;
			if( $status_tmp==self::STATUS_SUCCESS )
			{
				$info['status_str2'] = '���������';
				if( $info['sign_by']=='sys' )
				{
					$info['status_str2'] = '�ѳ�ʱ���';
				}
			}
			if( $status_tmp==self::STATUS_CLOSED )
			{
				$info['status_str2'] = '�ѹر�';
				if( intval($info['is_pay'])==1 )
				{
					$info['status_str2'] = '���˿�';
				}
			}

			//֧��ʱ��
			$pay_time_str = '--';
			if( $info['is_pay']==1 )
			{
				$pay_time_str = date('Y-m-d H:i:s', $info['pay_time']);
			}
			$info['pay_time_str'] = $pay_time_str;
			
			//��ȡ�������
			$info['buyer_name'] = get_user_nickname_by_user_id($info['buyer_user_id']);
			$info['buyer_icon'] = get_user_icon($info['buyer_user_id'], 165);
			
			//��ȡ��������
			$info['seller_name'] = get_seller_nickname_by_user_id($info['seller_user_id']);
			$info['seller_icon'] = get_seller_user_icon($info['seller_user_id'], 165);
			
			//������ϸ�б�
			if( $info['order_type']=='detail' )
			{
				$detail_list = $this->get_detail_list_all($info['order_id']);
				if( empty($detail_list) ) $detail_list = array();
				$info['detail'] = $detail_list;
				$info['expire_str'] = $this->order_detail_obj->get_expire_str($status_tmp, $detail_list, $cur_time);
			}
			elseif( $info['order_type']=='activity' )
			{
				$activity_list = $this->get_activity_list_all($info['order_id']);
				if( empty($activity_list) ) $activity_list = array();
				$info['activity'] = $activity_list;
				$info['expire_str'] = $this->order_activity_obj->get_expire_str($status_tmp, $activity_list, $cur_time);
			}

			//����ǩ�����б�
			$code_list = $this->get_code_list_all($info['order_id']);
			foreach($code_list as $code_key=>$code_info)
			{
				$hash = qrcode_hash($info['order_id'], $info['order_id'], $code_info['code_sn']);
				$code_info['qr_code_url'] = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$info['order_id']}&enroll_id={$info['order_id']}&code={$code_info['code_sn']}&hash={$hash}";
				$code_info['name'] = 'ǩ����';
				$code_list[$code_key] = $code_info;
			}
			$info['code_list'] = $code_list;
			
			//����״̬�����б�
			$process_list = $this->get_process_list_all($info['order_id'], 'process_id DESC');
			foreach($process_list as $process_key=>$process_info)
			{
				$process_nickname = '';
				$process_icon = '';
				if( $process_info['process_by']=='seller' )
				{
					$process_nickname = $info['seller_name'];
					$process_icon = $info['seller_icon'];
				}
				elseif( $process_info['process_by']=='buyer' )
				{
					$process_nickname = $info['buyer_name'];
					$process_icon = $info['buyer_icon'];
				}
				elseif( $process_info['process_by']=='sys' )
				{
					$process_nickname = $sys_nickname;
					$process_icon = $sys_icon;
				}
				
				$process_content_tmp = trim($process_info['process_content']);
				if( $login_user_id==$info['buyer_user_id'] )
				{
					$process_content_tmp = str_replace("{buyer_nickname}", "��", $process_content_tmp);
				}
				elseif( $login_user_id==$info['seller_user_id'] )
				{
					$process_content_tmp = str_replace("{seller_nickname}", "��", $process_content_tmp);
				}
				$process_content_tmp = str_replace("{seller_nickname}", $info['seller_name'], $process_content_tmp);
				$process_content_tmp = str_replace("{buyer_nickname}", $info['buyer_name'], $process_content_tmp);
				$process_content_tmp = str_replace("{sys_nickname}", $sys_nickname, $process_content_tmp);
				
				//������������������
				$overall_score_arr_tmp = array();
				$process_remark_tmp = '';
				$comment_info_tmp = '';
				if( $process_info['process_action']=='����' )
				{
					$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
					if( $process_info['process_by']=='seller' )
					{
						/*
						 * ��ȡ�̼Ҷ������߶�������Ʒ����
						* @param int $order_id
						* @param int $goods_id
						* @return array
						*/
						$comment_info_tmp = $mall_comment_obj->get_buyer_comment_info($info['order_id'], 0);
					}
					elseif( $process_info['process_by']=='buyer' )
					{
						/*
						 * ��ȡ�����߶��̼Ҷ�������Ʒ����
						* @param int $order_id
						* @param int $goods_id
						* @return array
						*/
						$comment_info_tmp = $mall_comment_obj->get_seller_comment_info($info['order_id'], 0);
					}
					if( $comment_info_tmp['overall_score']>0 )
					{
						$overall_score_arr_tmp = $mall_comment_obj->format_comment_score($comment_info_tmp['overall_score']);
					}
					$process_remark_tmp = trim($comment_info_tmp['comment']);
				}
				
				$process_info['process_nickname'] = $process_nickname;
				$process_info['process_icon'] = $process_icon;
				$process_info['process_time_str'] = date('Y.m.d H:i', $process_info['process_time']);
				$process_info['process_content'] = $process_content_tmp;
				$process_info['process_remark'] = $process_remark_tmp;
				$process_info['overall_score_arr'] = $overall_score_arr_tmp;
				$process_list[$process_key] = $process_info;
			}
			$info['process_list'] = $process_list;
			
			$list[$key] = $info;
		}
		return $list;
	}
	
	/**
	 * ��ȡ��ϸ
	 * @param int $order_detail_id
	 * @return array
	 */
	public function get_detail_info($order_detail_id)
	{
		return $this->order_detail_obj->get_detail_info($order_detail_id);
	}

	/**
	 * ��ȡ�����б�
	 * @param int $order_id ����ID
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_detail_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		return $this->order_detail_obj->get_detail_list($order_id, $b_select_count, $where_str, $order_by, $limit, $fields);
	}

	/**
	 * ��ȡ�����б�
	 * @param int $order_id ����ID
	 * @return array
	 */
	public function get_detail_list_all($order_id)
	{
		return $this->get_detail_list($order_id, false, '', 'order_detail_id ASC', '0,99999999');
	}

	/**
	 * ��ȡ���ϸ
	 * @param int $order_activity_id
	 * @return array
	 */
	public function get_activity_info($order_activity_id)
	{
		return $this->order_activity_obj->get_activity_info($order_activity_id);
	}

	/**
	 * ��ȡ��б�
	 * @param int $order_id ����ID
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_activity_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		return $this->order_activity_obj->get_activity_list($order_id, $b_select_count, $where_str, $order_by, $limit, $fields);
	}

	/**
	 * ��ȡ��б�
	 * @param int $order_id ����ID
	 * @return array
	 */
	public function get_activity_list_all($order_id)
	{
		return $this->get_activity_list($order_id, false, '', 'order_activity_id ASC', '0,99999999');
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_process($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_process_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $order_id ����ID
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_process_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_id = intval($order_id);
		
		//�����ѯ����
		$sql_where = '';
		if( $order_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_id={$order_id}";
		}
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_mall_order_process_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $order_id ����ID
	 * @param string $order_by
	 * @return array
	 */
	public function get_process_list_all($order_id, $order_by='process_id ASC')
	{
		return $this->get_process_list($order_id, false, '', $order_by, '0,99999999');
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_code($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_code_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param string $code_sn
	 * @return array
	 */
	public function get_code_info_recently($code_sn)
	{
		$code_sn = trim($code_sn);
		if( strlen($code_sn)<1 )
		{
			return array();
		}
		$where_str = 'code_sn=:x_code_sn';
		sqlSetParam($where_str, 'x_code_sn', $code_sn);
		$this->set_mall_order_code_tbl();
		return $this->find($where_str, 'code_id DESC');
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $order_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_code_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_id = intval($order_id);
		
		//�����ѯ����
		$sql_where = '';
		if( $order_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_id={$order_id}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_mall_order_code_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $order_id ����ID
	 * @return array
	 */
	public function get_code_list_all($order_id)
	{
		return $this->get_code_list($order_id, false, '', 'code_id ASC', '0,99999999');
	}
	
	/**
	 * ���ǩ�����Ƿ��ܸ���
	 * @param string $code_sn
	 * @return int
	 */
	private function check_code_recently($code_sn)
	{
		$code_sn = trim($code_sn);
		
		$where_str = 'code_sn=:x_code_sn';
		sqlSetParam($where_str, 'x_code_sn', $code_sn);
		
		$check_time = time() + 90*24*3600; //���90��
		$where_str .= " AND (is_check=0 OR (is_check=1 AND check_time>={$check_time}))";
		
		return $this->get_code_list(0, true, $where_str);
	}
	
	/**
	 * ������ǩ��
	 * @param int $code_id
	 * @param array $more_info array('check_time'=>0)
	 * @return boolean
	 */
	private function update_code_check($code_id, $more_info=array())
	{
		$code_id = intval($code_id);
		if( $code_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_check' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_mall_order_code_tbl();
		$affected_rows = $this->update($data, "code_id={$code_id} AND is_check=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��������
	 * @param int $status
	 * @param int $type_id
	 * @return int
	 */
	private function get_expire_seconds($status, $type_id)
	{
		$expire_seconds = 0;
		
		$status = intval($status);
		$type_id = intval($type_id);
		if( $status===self::STATUS_WAIT_PAY ) //��֧��
		{
			$expire_seconds_arr = array(
				31 => 1*3600, //ģ�ط���1Сʱ
				//31 => 300, //Ϊ������ԣ���ʱ5����
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 1*3600; //Ĭ��1Сʱ
				//$expire_seconds = 300; //Ϊ������ԣ���ʱ5����
			}
		}
		elseif( $status===self::STATUS_WAIT_CONFIRM ) //��ȷ��
		{
			$expire_seconds_arr = array(
				31 => 24*3600, //ģ�ط���24Сʱ
				//31 => 600, //Ϊ������ԣ���ʱ10����
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 24*3600; //Ĭ��24Сʱ
				//$expire_seconds = 600; //Ϊ������ԣ���ʱ10����
			}
		}
		elseif( $status===self::STATUS_WAIT_SIGN ) //��ǩ��
		{
			$expire_seconds_arr = array(
				31 => 48*3600, //ģ�ط���48Сʱ
				//31 => 900, //Ϊ������ԣ���ʱ15����
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 48*3600; //Ĭ��48Сʱ
				//$expire_seconds = 900; //Ϊ������ԣ���ʱ15����
			}
		}
		
		return $expire_seconds;
	}

	/**
	 * ��ʹ���Ż�ȯ
	 * @param array $order_info
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 */
	private function not_use_order_coupon($order_info)
	{
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		//������
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$total_amount = $order_info['total_amount']*1;
		$is_use_coupon = intval($order_info['is_use_coupon']);
		$is_pay = intval($order_info['is_pay']);
		
		//��鶩��
		if( $is_pay!=0 )
		{
			$result['result'] = -2;
			$result['message'] = '֧��״̬����';
			return $result;
		}
		if( $is_use_coupon==0 )
		{
			$result['result'] = 1;
			$result['message'] = '�˵�û��ʹ��ȯ';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//��ʹ���Ż�ȯ
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$not_use_ret = $coupon_obj->not_use_coupon_by_oid($this->channel_module, $order_id);
		if( $not_use_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -3;
			$result['message'] = $not_use_ret['message'];
			return $result;
		}
		
		//����Ϊ0
		$data = array(
			'discount_amount' => 0,
			'is_use_coupon' => 0,
			'coupon_sn' => '',
			'pending_amount' => $total_amount,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=1 AND is_pay=0 AND total_amount={$total_amount}");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '���²�ʹ���Ż�ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['used_amount'] = $not_use_ret['used_amount'];
		return $result;
	}
	
	/**
	 * �ӳ���������ʱ�� �������ڴ�֧��
	 * @param array $order_info
	 * @returns array('result'=>0, 'message'=>'', 'expire_time'=>0)
	 */
	private function delay_order_expire_time_by_wait_pay($order_info)
	{
		$result = array('result'=>0, 'message'=>'', 'expire_time'=>0);
		
		//������
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		$expire_time = intval($order_info['expire_time']);
		
		//��鶩��
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -2;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		//�ж��Ƿ���Ҫ�ӳ�
		$cur_time = time();
		$seconds = 15*60; //15����
		if( $expire_time<1 || $cur_time<($expire_time-$seconds) ) //����ʱ�����15����ʱ������
		{
			$result['result'] = 1;
			$result['message'] = '����Ҫ�ӳ�����ʱ��';
			$result['expire_time'] = $expire_time;
			return $result;
		}
		
		//�ӳ�ʱ��
		$expire_time_new = max($expire_time, $cur_time) + $seconds;
		$data = array(
			'expire_time' => $expire_time_new,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, " order_id={$order_id} AND status=" . self::STATUS_WAIT_PAY);
		if( !$ret )
		{
			$result['result'] = -3;
			$result['message'] = '�ӳ�����ʱ��ʧ��';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['expire_time'] = $expire_time_new;
		return $result;
	}

	/**
	 * �ύ���񶩵�
	 * @param int $buyer_user_id ����û�ID
	 * @param string $order_type �������� detail,activity
	 * @param array $detail_list ��Ʒ��ϸ�б�
	 * @param array $more_info ������Ϣ
	 * @return array array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'')
	 * @tutorial
	 *
	 * $detail_list = array( array(
	 * 	'goods_id' => 0, //��ƷID
	 * 	'prices_type_id' => '',
	 * 	'service_time' => 0, //����ʱ��
	 * 	'service_location_id' => '',
	 * 	'service_address' => '',
	 *  'service_people' => 0,
	 *  'prices' => 0, //���ۣ���������������������
	 * 	'quantity' => 0, //����
	 *  'goods_promotion_id' => 0, //��Ʒ����ID
	 * ), ... );
	 *
	 * $more_info = array(
	 * 	'seller_user_id' => 0, //�����û�ID����������������������
	 * 	'description' => '', //��������ע
	 *  'is_auto_accept' => 0, //�Ƿ��Զ����ܣ��µ���֧�������ܲ�����֪ͨ
	 *  'is_auto_sign' => 0, //�Ƿ��Զ�ǩ����ǩ�������۲�����֪ͨ
	 *  'referer' => '', //������Դ��app weixin pc wap oa
	 * );
	 *
	 */
	public function submit_order($buyer_user_id, $extend_list, $more_info=array())
	{
		return $this->order_detail_obj->submit_order($buyer_user_id, $extend_list, $more_info);
	}

	/**
	 * �ύ���񶩵�
	 * @param $buyer_user_id
	 * @param $extend_list
	 * @param array $more_info
	 * @return mixed
	 */
	public function submit_order_activity($buyer_user_id, $extend_list, $more_info=array())
	{
		return $this->order_activity_obj->submit_order($buyer_user_id, $extend_list, $more_info);
	}

	/**
	 * �����ļۣ��ܶ
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @param string $change_price ���ĺ�ļ۸�
	 * @param string $change_price_reason �ļ�����
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function change_order_price($order_sn, $user_id, $change_price, $change_price_reason)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		$change_price = $change_price*1;
		$change_price_reason = trim($change_price_reason);
		if( strlen($order_sn)<1 || $user_id<1 || $change_price<=0 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		$original_amount = $order_info['original_amount'];
		if( $change_price!=$original_amount && strlen($change_price_reason)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -2;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -3;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -3;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -3;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -3;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -3;
			$result['message'] = '����״̬����';
			return $result;
		}

		if( $change_price==$original_amount )
		{
			$is_change_price = 0;
			$change_price_reason = '';
		}
		else
		{
			$is_change_price = 1;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//��ȥ����һ�ε��Ż�ȯ
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -5;
			$result['message'] = '��ʹ���Ż�ȯʧ��';
			return $result;
		}

		$data = array(
			'total_amount' => $change_price,
			'pending_amount' => $change_price,
			'is_change_price' => $is_change_price,
			'change_price_reason' => $change_price_reason,
			'lately_time' => time(),
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=0 AND status IN (" . self::STATUS_WAIT_PAY .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -12;
			$result['message'] = '��������Ϣ�ļ�ʧ��';
			return $result;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->change_order_price_after($trigger_params);

		//�ı���־
		$log_arr = array(
			'order_sn' => $order_sn,
			'seller_user_id'=> $user_id,
			'change_price'=>$change_price,
			'change_price_reason'=>$change_price_reason,
			'change_time'=>time(),
		);
		pai_log_class::add_log($log_arr, 'change_order_price', 'pai_mall_order_class');		

		$result['result'] = 1;
		$result['message'] = '�ļ۳ɹ�';
		return $result;
	}
	
	/**
	 * ׼������֧��
	 * @param string $order_sn
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function ready_pay_order($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -2;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -3;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -3;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -3;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -3;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -3;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		//�ӳ���������ʱ�䣬�Ա���û�����֧��ʱ��
		$delay_ret = $this->delay_order_expire_time_by_wait_pay($order_info);
		if( $delay_ret['result']!=1 )
		{
			$result['result'] = -4;
			$result['message'] = $delay_ret['message'];
			return $result;
		}
		
		//��ʹ���Ż�ȯ
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $not_use_ret['message'];
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = ''; //Ϊ��ʱ��ǰ�˾Ͳ��ᵯ����ʾ�ˣ�ֱ����ת��
		return $result;
	}

	/**
	 * ����֧��ҳ�Ľ��Ա���ʾ
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param string $coupon_sn
	 * @return array
	 */
	public function cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn='')
	{
		$result = array('result'=>0, 'message'=>'', 'response_data'=>array());

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		$is_available_balance = intval($is_available_balance);
		$coupon_sn = trim($coupon_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$is_order_promotion = intval($order_info['is_order_promotion']);
		$order_promotion_id = intval($order_info['order_promotion_id']);
		$total_amount = $order_info['total_amount']*1;
		$cur_amount = $total_amount;
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -4;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -5;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -5;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -5;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -5;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -5;
			$result['message'] = '����״̬����';
			return $result;
		}

		//��ȡ������ϸ�б�
		$extend_list = array();
		$is_goods_promotion = 0;
		$goods_promotion_id = 0;
		if( $order_info['order_type']=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_goods_promotion']);
			$goods_promotion_id = intval($extend_list[0]['goods_promotion_id']);
		}
		elseif( $order_info['order_type']=='activity' )
		{
			$extend_list = $this->get_activity_list($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_activity_promotion']);
			$goods_promotion_id = intval($extend_list[0]['activity_promotion_id']);
		}
		if( empty($extend_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}

		$response_data = array(
			'total_amount' => number_format($total_amount, 2, '.', ''),
			'is_allow_coupon' => 1, //�Ƿ�ʹ���Ż�ȯ
			'coupon_sn' => '', //�Ż�ȯ��
			'batch_name' => '', //�Ż�ȯ����
			'coupon_amount' => 0, //�Ż�ȯ���
			'is_available_balance' => $is_available_balance, //�Ƿ�ʹ��Ǯ��
			'available_balance' => 0, //Ǯ��ʣ����
			'use_balance' => 0, //Ǯ��ʹ�ý��
			'is_use_third_party_payment' => 1, //�Ƿ�ʹ�õ�����֧����ʽ
			'pending_amount' => 0, //����֧���۸�
		);

		//�Ƿ�����ʹ���Ż�ȯ
		if( $is_order_promotion==1 )
		{
			$order_promotion_info = POCO::singleton('pai_promotion_class')->get_promotion_full_info($order_promotion_id);
			if( empty($order_promotion_info) || $order_promotion_info['is_allow_coupon']!=1 )
			{
				$response_data['is_allow_coupon'] = 0;
			}
		}
		if( $is_goods_promotion==1 )
		{
			$goods_promotion_info = POCO::singleton('pai_promotion_class')->get_promotion_full_info($goods_promotion_id);
			if( empty($goods_promotion_info) || $goods_promotion_info['is_allow_coupon']!=1 )
			{
				$response_data['is_allow_coupon'] = 0;
			}
		}

		//�����Ż�ȯ
		if( strlen($coupon_sn)>0 && $response_data['is_allow_coupon']==1 )
		{
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$coupon_detail_info = $coupon_obj->get_coupon_detail_by_sn($coupon_sn, $buyer_user_id);
			$coupon_used_amount = $coupon_obj->cal_used_amount($coupon_detail_info, array( 'order_total_amount'=>$total_amount ));
			$response_data['coupon_sn'] = $coupon_sn;
			$response_data['batch_name'] = trim($coupon_detail_info['batch_name']);
			$response_data['coupon_amount'] = $coupon_used_amount;
			if( $response_data['coupon_amount']>0 )
			{
				$cur_amount = $cur_amount - $response_data['coupon_amount'];
			}
		}

		//��ȡǮ����Ϣ
		$payment_obj = POCO::singleton('pai_payment_class');
		$account_info = $payment_obj->get_user_account_info($buyer_user_id);

		$response_data['available_balance'] = $account_info['available_balance'];
		if( $is_available_balance==1 )
		{
			if( $response_data['available_balance']<$cur_amount )
			{
				$response_data['is_use_third_party_payment'] = 1;
				$response_data['use_balance'] = $response_data['available_balance'];
			}
			else
			{
				$response_data['is_use_third_party_payment'] = 0;
				$response_data['use_balance'] = $cur_amount;
			}
		}
		$response_data['pending_amount'] = number_format(($cur_amount-$response_data['use_balance']), 2, '.', '');

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['response_data'] = $response_data;
		return $result;
	}

	/**
	 * ��ȡ�Ż�ȯ������Ϣ
	 * @param string $order_sn ������
	 * @param int $user_id �������û�ID
	 * @return array
	 */
	public function get_coupon_param_info_by_order_sn($order_sn, $user_id)
	{
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			return array();
		}
		
		//��������
		$order_info = $this->get_order_info($order_sn);
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			return array();
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info);
		$mall_type_id = intval($order_info['type_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$order_total_amount = trim($order_info['total_amount']);
		
		$param_info = array(
			'channel_module' => $this->channel_module,
			'channel_oid' => $order_id,
			'module_type' => $this->channel_module, // yuepai waipai
			'order_total_amount' => $order_total_amount, // �����ܶ�
			'model_user_id' => $seller_user_id, // ģ���û�ID�����ݾ�Լ��ȯ������
			'event_user_id' => $seller_user_id, // ��֯���û�ID���������ĵ�����
			'org_user_id' => $org_user_id, // ����ID
			'mall_type_id' => $mall_type_id, //����Ʒ��
			'seller_user_id' => $seller_user_id, //�����û�ID
		);
		if( $order_type=='detail' )
		{
			$detail_info = $this->get_detail_info($order_id);
			$param_info['mall_goods_id'] = $detail_info['goods_id'];//��ƷID
		}
		elseif( $order_type=='activity' )//TODO �Ż�ȯactivity_id or goods_id
		{
			$activity_info = $this->get_activity_info($order_id);
			$param_info['mall_activity_id'] = $activity_info['activity_id'];//�ID
		}

		return $param_info;
	}

	/**
	 * �ύ֧��
	 * @param string $order_sn
	 * @param int $user_id ����û�ID
	 * @param double $available_balance ҳ�浱ǰ���
	 * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
	 * @param string $third_code ֧����ʽ alipay_purse tenpay_wxapp�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $notify_url
	 * @param string $coupon_sn
	 * @param array $more_info array('page_total_amount'=>0)
	 * @return array array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'')
	 * result 1��ȡ������֧����2���֧���ɹ�
	 */
	public function submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url='', $coupon_sn='', $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		$available_balance = number_format($available_balance*1, 2, '.', '')*1;
		$is_available_balance = intval($is_available_balance);
		$third_code = trim($third_code);
		$redirect_url = trim($redirect_url);
		$notify_url = trim($notify_url);
		$coupon_sn = trim($coupon_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$page_total_amount = $more_info['page_total_amount']*1;

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$type_id = intval($order_info['type_id']);
		$is_order_promotion = intval($order_info['is_order_promotion']);
		$order_promotion_id = intval($order_info['order_promotion_id']); //��������ID
		$total_amount = $order_info['total_amount']*1;
		$pending_amount = $total_amount;
		$status = intval($order_info['status']);
		$is_auto_accept = intval($order_info['is_auto_accept']);

		//��鶩��
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		if( $page_total_amount>0 && $page_total_amount!=$total_amount )
		{
			$result['result'] = -4;
			$result['message'] = '���������󣬿����Ѹļ�';
			return $result;
		}

		//��ȡ������ϸ�б�
		$extend_list = array();
		$is_goods_promotion = 0;
		$goods_promotion_id = 0;
		$goods_id = 0;
		$goods_name = 0;
		if( $order_info['order_type']=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_goods_promotion']);
			$goods_promotion_id = intval($extend_list[0]['goods_promotion_id']);
			$goods_id = intval($extend_list[0]['goods_id']);
			$goods_name = trim($extend_list[0]['goods_name']);
		}
		elseif( $order_info['order_type']=='activity' )
		{
			$extend_list = $this->get_activity_list($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_activity_promotion']);
			$goods_promotion_id = intval($extend_list[0]['activity_promotion_id']);
			$goods_id = intval($extend_list[0]['goods_id']);
			$goods_name = trim($extend_list[0]['goods_name']);
		}
		if( empty($extend_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}

		//�������˵ĵ��Ƿ����ʹ���Ż�ȯ
		if( strlen($coupon_sn)>0 && ($is_order_promotion==1 || $is_goods_promotion==1) )
		{
			$promotion_obj = POCO::singleton('pai_promotion_class');
			if( $is_order_promotion==1 )
			{
				$order_promotion_info = $promotion_obj->get_promotion_info($order_promotion_id);
				if( empty($order_promotion_info) || $order_promotion_info['is_allow_coupon']!=1 )
				{
					$result['result'] = -5;
					$result['message'] = '��������������ʹ���Ż�ȯ';
					return $result;
				}
			}
			if( $is_goods_promotion==1 )
			{
				$goods_promotion_info = $promotion_obj->get_promotion_info($goods_promotion_id);
				if( empty($goods_promotion_info) || $goods_promotion_info['is_allow_coupon']!=1 )
				{
					$result['result'] = -5;
					$result['message'] = '��Ʒ����������ʹ���Ż�ȯ';
					return $result;
				}
			}
		}

		$cur_time = time();

		//��ȥ����һ�ε��Ż�ȯ
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = '��ʹ���Ż�ȯʧ��';
			return $result;
		}

		$discount_amount = 0;
		$is_use_coupon = 0;
		if( strlen($coupon_sn)>0 )
		{
			//����ʼ
			POCO_TRAN::begin($this->getServerId());

			$param_info = array(
				'module_type' => $this->channel_module, //ģ������ waipai yuepai topic task_request mall_order
				'order_total_amount' => $total_amount, //�����ܽ��
				'model_user_id' => $seller_user_id, //ģ���û�ID�����ݾ�Լ��ȯ������
				'event_user_id' => $seller_user_id, //��֯���û�ID���������ĵ�����
				'org_user_id' => $org_user_id, //����ID,
				'mall_type_id' => $type_id, //��ƷƷ��ID
				'seller_user_id' => $seller_user_id, //�����û�ID
				'mall_goods_id' => $goods_id, //��ƷID
			);
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$coupon_ret = $coupon_obj->use_coupon($buyer_user_id, 1, $coupon_sn, $this->channel_module, $order_id, $param_info);
			if( $coupon_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = $coupon_ret['message'];
				return $result;
			}
			$discount_amount = $coupon_ret['used_amount'];
			$is_use_coupon = 1;

			$pending_amount = $pending_amount - $discount_amount;
			if( $pending_amount<=0 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = '�Żݽ������';
				return $result;	//�Ż�ȯ�����ڶ�������ʹ�ɹ�ʹ��Ҳ�˻ء�
			}

			$data = array(
				'discount_amount' => $discount_amount,
				'is_use_coupon' => $is_use_coupon,
				'coupon_sn' => $coupon_sn,
				'pending_amount' => $pending_amount,
			);
			$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=0 AND is_pay=0 AND total_amount={$total_amount}");
			if( !$ret )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = '�����Żݽ��ʧ��';
				return $result;
			}

			//�����ύ
			POCO_TRAN::commmit($this->getServerId());
		}

		$payment_obj = POCO::singleton('pai_payment_class');

		//�����֧��
		if( $is_available_balance )
		{
			$account_info = $payment_obj->get_user_account_info($buyer_user_id);
			if( $account_info['available_balance']<$pending_amount )
			{
				//���㣬�ñ꣬����ʹ�õ�����
				$amount = $pending_amount - $account_info['available_balance'];
				$redirect_third = 1;
			}
			else
			{
				//����ʼ
				POCO_TRAN::begin($this->getServerId());

				//�����û������֧�������¶���״̬
				$more_info = array(
					'org_user_id' => $org_user_id,
					'is_balance' => 1,
					'is_third' => 0,
					'recharge_id' => 0,
					'subject' => $goods_name,
					'remark' => '',
				);
				$submit_ret = $payment_obj->submit_trade_out_v2($this->channel_module, $order_id, $order_id, $buyer_user_id, $total_amount, $discount_amount, $more_info);
				if( $submit_ret['error']!==0 )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -6;
					$result['message'] = $submit_ret['message'];
					return $result;
				}

				//֧������
				$pay_ret = $this->pay_order($order_info);
				if( !$pay_ret )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -7;
					$result['message'] = '֧��ʧ��';
					return $result;
				}

				//�����ύ
				POCO_TRAN::commmit($this->getServerId());

				//�¼�����
				$trigger_params = array(
					'order_sn' => $order_sn,
				);
				POCO::singleton('pai_mall_trigger_class')->pay_order_after($trigger_params);

				//֧�����Զ�����
				if( $is_auto_accept==1 )
				{
					$this->accept_order_for_system($order_sn);
				}

				$result['result'] = 2;
				$result['message'] = '���֧���ɹ�';
				return $result;
			}
		}
		else
		{
			//ֱ���õ�����֧��
			$amount = $pending_amount;
			$redirect_third = 1;
		}

		if( $redirect_third )
		{
			$openid = '';
			if( $third_code=='tenpay_wxpub' )
			{
				$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
				$bind_info = $bind_weixin_obj->get_bind_info_by_user_id( $buyer_user_id );
				$openid = $bind_info['open_id'];
				if( empty($openid) )
				{
					$result['result'] = -8;
					$result['message'] = 'û�а�΢�ź�';
					return $result;
				}
			}
			$more_info = array(
				'channel_return' => $redirect_url,
				'channel_notify' => $notify_url,
				'openid' => $openid,
			);
			$recharge_ret = $payment_obj->submit_recharge($this->channel_module, $buyer_user_id, $amount, $third_code, $order_id, $order_id, 0, $more_info);
			if( $recharge_ret['error']!==0 )
			{
				$result['result'] = -9;
				$result['message'] = $recharge_ret['message'];//��ת��������֧����������  ��ϸ��Ϣ��recharge_ret';
				return $result;
			}

			$result['result'] = 1;
			$result['message'] = '����ת��������֧����';
			$result['payment_no'] = trim($recharge_ret['payment_no']);
			$result['request_data'] = trim($recharge_ret['request_data']);
			return $result;
		}

		$result['result'] = -10;
		$result['message'] = 'δ֪����';
		return $result;
	}

	/**
	 * ֧������
	 * @param array $order_info
	 * @return bool
	 */
	private function pay_order($order_info)
	{
		if( !is_array($order_info) || empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$order_sn = trim($order_info['order_sn']);
		$order_type = trim($order_info['order_type']);
		$type_id = intval($order_info['type_id']);

		$cur_time = time();

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//����֧��״̬�������û�д�ȷ�ϲ���/״̬��
		$status_tmp = 0;
		if( $order_type=='detail' )
		{
			$status_tmp = self::STATUS_WAIT_CONFIRM;
		}
		elseif( $order_type=='activity' )
		{
			$status_tmp = self::STATUS_WAIT_SIGN;
		}
		$expire_time = $cur_time + $this->get_expire_seconds($status_tmp, $type_id);

		$data = array(
			'status' => $status_tmp,
			'is_pay' => 1,
			'pay_time' => $cur_time,
			'expire_time' => $expire_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_pay=0 AND status IN (" . self::STATUS_WAIT_PAY .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '֧��',
			'process_result' => '��ȷ��',
			'process_content' => '{buyer_nickname} ��֧������',
			'process_time' => $cur_time,
		);
		$process_id = $this->add_process($data);
		if( $process_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		return true;
	}
	
	/**
	 * ֧������������֧����Ϣ
	 * @param array $payment_info
	 * @return array array('result'=>0, 'message'=>'') result 1,֧���ɹ���else ֧��ʧ��
	 */
	public function pay_order_by_payment_info($payment_info)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		if( !is_array($payment_info) || empty($payment_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		$payment_status = intval($payment_info['status']);
		if( $payment_info['channel_module']!='recharge' || $payment_status!=8 )
		{
			$result['result'] = -2;
			$result['message'] = '֧������';
			return $result;
		}
		$payment_no = trim($payment_info['payment_no']);
		$third_total_fee = $payment_info['third_total_fee']*1; //ʵ�ս��
		$channel_param = trim($payment_info['channel_param']);
		
		if($third_total_fee<=0 )
		{
			$result['result'] = -3;
			$result['message'] = '������';
			return $result;
		}
		if( strlen($channel_param)<1 )
		{
			$result['result'] = -4;
			$result['message'] = '֧������';
			return $result;
		}
		$channel_param_arr = unserialize($channel_param);
		if( empty($channel_param_arr) )
		{
			$result['result'] = -5;
			$result['message'] = '֧������';
			return $result;
		}
		$order_id = intval($channel_param_arr['enroll_id_str']);
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -6;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$is_use_coupon = intval($order_info['is_use_coupon']);
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = $order_info['pending_amount']*1;
		$is_auto_accept = intval($order_info['is_auto_accept']);
		
		//��ȡ������ϸ�б�
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$goods_name = $extend_list[0]['goods_name'];
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
			$goods_name = $extend_list[0]['activity_name'];
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -7;
			$result['message'] = '��������Ϊ��';
			return $result;
		}

		//��֧��
		if( $order_info['is_pay']==1 )
		{
			if( $payment_no==$order_info['payment_no'] )
			{
				$result['result'] = 1;
				$result['message'] = '�ɹ�';
				return $result;
			}
			
			$result['result'] = -8;
			$result['message'] = '�ظ�֧��';
			return $result;
		}
		
		//ȫ���õ�����Ϊ0�������������һ��ʹ��Ϊ1
		if( $pending_amount>$third_total_fee )
		{
			$is_balance = 1;
		}
		else
		{
			$is_balance = 0;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		$more_info = array(
			'org_user_id' => $org_user_id,
			'is_balance' => $is_balance,
			'is_third' => 1,
			'recharge_id' => $payment_info['channel_rid'],
			'subject' => $goods_name,
			'remark' => '',
		);
		$payment_obj = POCO::singleton('pai_payment_class');
		$submit_ret = $payment_obj->submit_trade_out_v2($this->channel_module, $order_id, $order_id, $buyer_user_id, $total_amount, $discount_amount, $more_info);
		if( $submit_ret['error']!==0 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -9;
			$result['message'] = $submit_ret['message'];
			return $result;
		}
		
		$pay_ret = $this->pay_order($order_info);
		if( !$pay_ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -10;
			$result['message'] = '֧��ʧ��';
			return $result;
		}
		
		$this->update_order(array('payment_no'=>$payment_no), $order_id);
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->pay_order_after($trigger_params);
		
		//֧�����Զ�����
		if( $is_auto_accept==1 )
		{
			$this->accept_order_for_system($order_sn);
		}
		
		$result['result'] = 1;
		$result['message'] = '֧���ɹ�';
		return $result;
	}
	
	/**
	 * ���ң����ܶ���
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function accept_order($order_sn, $user_id=0)
	{
		return $this->order_detail_obj->accept_order($order_sn, $user_id);
	}
	
	/**
	 * ϵͳ�����ܶ���
	 * @param string $order_sn
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function accept_order_for_system($order_sn)
	{
		return $this->order_detail_obj->accept_order_for_system($order_sn);
	}
	
	/**
	 * �ܾ�����
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refuse_order($order_sn, $user_id=0)
	{
		return $this->order_detail_obj->close_wait_confirm_order_for_seller($order_sn, $user_id);
	}
	
	/**
	 * ���ҹرն���
	 * ����֧�������˿�
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_seller($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		//�رն���
		if( $status===self::STATUS_WAIT_PAY )
		{
			return $this->close_wait_pay_order_for_seller($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_CONFIRM )
		{
			return $this->order_detail_obj->close_wait_confirm_order_for_seller($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_SIGN )
		{
			return $this->close_wait_sign_order_for_seller($order_sn, $user_id);
		}
		
		$result['result'] = -5;
		$result['message'] = 'δ֪����';
		return $result;
	}
	
	/**
	 * ��ҹرն���
	 * ����֧�������˿�
	 * @param string $order_sn
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		//�رն���
		if( $status===self::STATUS_WAIT_PAY )
		{
			return $this->close_wait_pay_order_for_buyer($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_CONFIRM )
		{
			return $this->close_wait_confirm_order_for_buyer($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_SIGN )
		{
			return $this->close_wait_sign_order_for_buyer($order_sn, $user_id);
		}
		
		$result['result'] = -5;
		$result['message'] = 'δ֪����';
		return $result;
	}
	
	/**
	 * ��������˿�
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer($order_sn, $user_id)
	{
		return $this->close_wait_sign_order_for_buyer($order_sn, $user_id);
	}
	
	/**
	 * ϵͳ�رն���
	 * ����֧�������˿�
	 * @param string $order_sn
	 * @param string $reason �ر�ԭ��
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_system($order_sn,$reason='')
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		//�رն���
		if( $status===self::STATUS_WAIT_PAY )
		{
			return $this->close_wait_pay_order_for_system($order_sn);
		}
		elseif( $status===self::STATUS_WAIT_CONFIRM )
		{
			return $this->close_wait_confirm_order_for_system($order_sn);
		}
		elseif( $status===self::STATUS_WAIT_SIGN )
		{
			return $this->close_wait_sign_order_for_system($order_sn,$reason);
		}
		
		$result['result'] = -5;
		$result['message'] = 'δ֪����';
		return $result;
	}
	
	/**
	 * ��ң��رմ�֧������
	 * @param string $order_sn ������
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);
		
		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�رն���
		$more_info = array(
			'close_by' => 'buyer',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{buyer_nickname} �ѹرն���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->close_wait_pay_order_for_buyer_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}
	
	/**
	 * ���ң��رմ�֧������
	 * @param string $order_sn ������
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_seller($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�رն���
		$more_info = array(
			'close_by' => 'seller',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{seller_nickname} �ѹرն���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->close_wait_pay_order_for_seller_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}
	
	/**
	 * ϵͳ���رմ�֧������
	 * @param string $order_sn ������
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_system($order_sn)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�رն���
		$more_info = array(
			'close_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{sys_nickname} ������ʱδ֧���ѹر�',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}
	
	/**
	 * ��ң��رմ�ȷ�϶���
	 * @param string $order_sn ������
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_confirm_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�رն���
		$more_info = array(
			'close_by' => 'buyer',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{buyer_nickname} ��ȡ������',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->close_wait_confirm_order_for_buyer_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ϵͳ���رմ�ȷ�϶���
	 * @param string $order_sn ������
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_confirm_order_for_system($order_sn)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�رն���
		$more_info = array(
			'close_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{sys_nickname} ������ʱδ�����ѹر�',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}
	
	/**
	 * ��ң��رմ�ǩ������
	 * @param string $order_sn ������
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		$order_type = trim($order_info['order_type']);

		//��ȡ������ϸ�б�
		//TODO �����ʱ����ȷ��
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -6;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		$service_time = $extend_list[0]['service_time']*1;
		
		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�Ƿ������˿�
		$allow_time = self::ALLOW_BUYER_REFUND_TIME;
		$service_time_prev = $service_time - $allow_time*3600; //ǰ12Сʱ
		if( $service_time_prev<$cur_time )
		{
			$result['result'] = -5;
			$result['message'] = '�������ʼ����12Сʱ�����������˿�';
			return $result;
		}
		
		//�رն���
		$more_info = array(
			'close_by' => 'buyer',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -6;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{buyer_nickname} ������Чʱ���������˿�˻������յ�����',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->close_wait_sign_order_for_buyer_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}
	
	/**
	 * ���ң��رմ�ǩ������
	 * @param string $order_sn ������
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_seller($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�رն���
		$more_info = array(
			'close_by' => 'seller',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{seller_nickname} �ر��˶���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}
	
	/**
	 * ϵͳ���رմ�ǩ������
	 * @param string $order_sn ������
	 * @param string $reason �ر�ԭ��
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_system($order_sn,$reason)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$reason = trim($reason);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//�رն���
		$more_info = array(
			'close_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}

		if( strlen($reason)<1 )
		{
			$reason = '{sys_nickname} ϵͳ�ر��˶���';
		}
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => 'ϵͳ�رգ�ԭ��'.$reason,
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->close_wait_sign_order_for_system_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}
	
	/**
	 * �رն���
	 * @param array $order_info
	 * @param array $more_info array( 'close_by'=>'', 'cur_time'=>0 )
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function _close_order($order_info, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info['order_type']);
		$total_amount = $order_info['total_amount']*1;
		$is_pay = intval($order_info['is_pay']);
		$status = intval($order_info['status']);
		$is_special = intval($order_info['is_special']);
		
		if( !is_array($more_info) ) $more_info = array();
		$close_by = trim($more_info['close_by']);
		$cur_time = intval($more_info['cur_time']);
		if( $cur_time<1 ) $cur_time = time();
		
		//��鶩��
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -3;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����״̬
		$data = array(
			'status' => self::STATUS_CLOSED,
			'close_time' => $cur_time,
			'close_by' => $close_by,
			'close_status' => $status,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND status={$status}");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}

		//���������ϸ�б�
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -5;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		
		//�������
		$goods_obj = POCO::singleton('pai_mall_goods_class');
		foreach($extend_list as $detail_info)
		{
			$goods_id_tmp = $detail_info['goods_id'];
			$goods_quantity_tmp = $detail_info['quantity'];
			$prices_type_id_tmp = $detail_info['prices_type_id'];
			if( $order_type=='activity' ) $stage_id = $detail_info['stage_id'];
			if( !$is_special ) //��������
			{
				$change_ret = $goods_obj->change_goods_stock($goods_id_tmp, $goods_quantity_tmp, $prices_type_id_tmp); //ͨ���ӿ��޸���Ʒ���
				if( $change_ret!=1 )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());
					
					$result['result'] = -6;
					$result['message'] = "������ʧ��";
					return $result;
				}
			}
		}
		
		//�˻���������
		//TODO �˻��������ܻ���Ҫ����order_type
		$promotion_rst = POCO::singleton('pai_promotion_class')->refund_promotion_by_oid($this->channel_module, $order_id);
		if( $promotion_rst['result']!==1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -7;
			$result['message'] = $promotion_rst['message'];
			return $result;
		}
		
		//����֧�������˿��ȯ
		if( $is_pay==1 )
		{
			$payment_obj = POCO::singleton('pai_payment_class');
			$cancel_ret = $payment_obj->cancel_event_v2($this->channel_module, $order_id);
			if( $cancel_ret['error']!==0 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());
		
				$result['result'] = -8;
				$result['message'] = 'ȡ������ʧ��';
				return $result;
			}
			
			//���Ż�ȯ
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$refund_ret = $coupon_obj->refund_coupon_by_oid($this->channel_module, $order_id);
			if( $refund_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());
		
				$result['result'] = -9;
				$result['message'] = '�˻��Ż�ȯʧ��';
				return $result;
			}
		}
		else
		{
			//��δ�����ʹ���Ż�ȯ
			$not_use_ret = $this->not_use_order_coupon($order_info);
			if( $not_use_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -10;
				$result['message'] = '��ʹ���Ż�ȯʧ��';
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
	 * ǩ������
	 * �����������ǩ������ҳ�ʾǩ���룬���ҵ���ɨ�뾵ͷ��
	 * @param string $code_sn ǩ����
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order($code_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);
		
		//������
		$code_sn = trim($code_sn);
		$user_id = intval($user_id);
		if( strlen($code_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);
		
		//��ȡǩ������Ϣ
		$code_info = $this->get_code_info_recently($code_sn);
		if( empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ�������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$order_id = intval($code_info['order_id']);
		$is_check = intval($code_info['is_check']);
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			$result['is_limit_error'] = 0;
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '��ǩ���벻��ȷ�������³���';
			$result['is_limit_error'] = 1;
			return $result;
		}
		if( $is_check==1 )
		{
			$result['result'] = -4;
			$result['message'] = '��ǩ����֮ǰ�Ѿ�ǩ����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $is_check!=0 )
		{
			$result['result'] = -4;
			$result['message'] = 'ǩ����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		
		$cur_time = time();
		
		//ǩ������
		$more_info = array(
			'sign_by' => 'buyer',
			'cur_time' => $cur_time,
		);
		$sign_ret = $this->_sign_order($code_info, $order_info, $more_info);
		if( $sign_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $sign_ret['message'];
			$result['order_sn'] = $sign_ret['order_sn'];
			$result['is_limit_error'] = $sign_ret['is_limit_error'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => 'ǩ��',
			'process_result' => '�����',
			'process_content' => '{buyer_nickname} �ѳɹ�ǩ��',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->sign_order_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}
	
	/**
	 * ϵͳǩ������
	 * @param string $order_sn
	 * @param boolean $is_expired �Ƿ���Ϊ���ڣ�����ϵͳǩ��
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order_for_system($order_sn, $is_expired=false)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);
		
		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			$result['is_limit_error'] = 0;
			return $result;
		}
		$order_id = trim($order_info['order_id']);
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);
		
		//��ȡǩ����
		$code_list = $this->get_code_list_all($order_id);
		$code_info = $code_list[0];
		if( !is_array($code_list) || empty($code_list) || !is_array($code_info) || empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ�������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$is_check = intval($code_info['is_check']);
		
		//��鶩��
		if( $is_check==1 )
		{
			$result['result'] = -4;
			$result['message'] = 'ǩ������ǩ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $is_check!=0 )
		{
			$result['result'] = -4;
			$result['message'] = 'ǩ����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		
		$cur_time = time();
		
		//ǩ������
		$more_info = array(
			'sign_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$sign_ret = $this->_sign_order($code_info, $order_info, $more_info);
		if( $sign_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $sign_ret['message'];
			$result['order_sn'] = $sign_ret['order_sn'];
			$result['is_limit_error'] = $sign_ret['is_limit_error'];
			return $result;
		}
		
		//��������
		if( $is_expired )
		{
			$process_content = '{sys_nickname} ������ʱδǩ�����������Զ������̼��˻�';
		}
		else
		{
			$process_content = '{sys_nickname} �ѳɹ�ǩ��';
		}
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => 'ǩ��',
			'process_result' => '�����',
			'process_content' => $process_content,
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->sign_order_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}
	
	/**
	 * ǩ������
	 * @param array $code_info
	 * @param array $order_info
	 * @param array $more_info array( 'sign_by'=>'', 'cur_time'=>0 )
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	private function _sign_order($code_info, $order_info, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);
		
		//������
		if( !is_array($code_info) || empty($code_info) || !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$is_check = intval($code_info['is_check']);
		$order_id = intval($order_info['order_id']);
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);
		
		if( !is_array($more_info) ) $more_info = array();
		$sign_by = trim($more_info['sign_by']);
		$cur_time = intval($more_info['cur_time']);
		if( $cur_time<1 ) $cur_time = time();
		
		//��鶩��
		if( $is_check!=0 )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -3;
			$result['message'] = '����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����ǩ����״̬
		$more_info = array(
			'check_time' => $cur_time,
		);
		$ret = $this->update_code_check($code_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '����ǩ��״̬ʧ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		
		//���¶���״̬
		$data = array(
			'status' => self::STATUS_SUCCESS,
			'sign_time' => $cur_time,
			'sign_by' => $sign_by,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND status IN (" . self::STATUS_WAIT_SIGN .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '���¶���״̬ʧ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		
		//���㶩��
		$end_ret = $this->end_order($order_id);
		if( $end_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '���㶩��ʧ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}


	/**
	 * ���㶩��
	 * @param int $order_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function end_order($order_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$order_id = intval($order_id);
		if( $order_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = bcsub($total_amount, $discount_amount, 2);

		//���������ϸ�б�
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$goods_name = trim($extend_list[0]['goods_name']);
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
			$goods_name = trim($extend_list[0]['activity_name']);
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -6;
			$result['message'] = '��������Ϊ��';
			return $result;
		}

		//��������
		$org_amount = $org_user_id>0?$pending_amount:0;
		$in_list[] = array(
			'discount_amount' => $discount_amount,
			'user_id' => $seller_user_id,
			'org_user_id' => $org_user_id,
			'apply_id' => 0,
			'amount' => $total_amount,
			'org_amount' => $org_amount,
			'subject' => $goods_name,
			'remark' => '',
		);

		//�����Ż�ȯ
		$coupon_cash_list = array();
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$ref_order_list = $coupon_obj->get_ref_order_list_by_oid($this->channel_module, $order_id);
		if( !is_array($ref_order_list) ) $ref_order_list = array();
		foreach( $ref_order_list as $ref_order_info )
		{
			$used_amount_tmp = $ref_order_info['used_amount'];
			$org_amount_tmp = $org_user_id>0?$used_amount_tmp:0;
			$coupon_cash_list[] = array(
				'id' => $ref_order_info['id'],
				'user_id' => $seller_user_id,
				'org_user_id' => $org_user_id,
				'amount' => $used_amount_tmp,
				'org_amount' => $org_amount_tmp,
				'subject' => $goods_name,
				'remark' => '',
			);
		}

		$refund_list = array();
		$coupon_refund_list = array();
		$payment_obj = POCO::singleton('pai_payment_class');
		$end_ret = $payment_obj->end_event_v2($this->channel_module, $order_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
		if( $end_ret['error']!==0 )
		{
			$result['result'] = -3;
			$result['message'] = '��ɶ���ʧ��';
			return $result;
		}

		//�������
		$promotion_obj = POCO::singleton('pai_promotion_class');
		$ref_order_list = $promotion_obj->get_ref_order_list_by_oid($this->channel_module, $order_id);
		if( !is_array($ref_order_list) ) $ref_order_list = array();
		foreach( $ref_order_list as $ref_order_info )
		{
			$more_info = array('seller_user_id'=>$seller_user_id, 'org_user_id'=>$org_user_id, 'need_amount'=>0, 'org_amount'=>0, 'subject'=>$goods_name);
			$cash_rst = $promotion_obj->cash_promotion($ref_order_info['id'], 0, $more_info);
			if( $cash_rst['result']!=1 )
			{
				$result['result'] = -4;
				$result['message'] = '���ִ���ʧ��';
				return $result;
			}
			$settle_rst = $promotion_obj->settle_promotion($ref_order_info['id']);
			if( $settle_rst['result']!=1 )
			{
				$result['result'] = -5;
				$result['message'] = '�������ʧ��';
				return $result;
			}
		}

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}


	/**
	 * ������۶���
	 * @param int $order_id
	 * @param int $user_id ����û�ID
	 * @param int $is_anonymous �Ƿ�����
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function comment_order_for_buyer($order_id, $user_id, $is_anonymous)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_id = intval($order_id);
		$user_id = intval($user_id);
		$is_anonymous = intval($is_anonymous);
		if( strlen($order_id)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status!==self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//����״̬
		$data = array(
			'is_buyer_comment' => 1,
			'buyer_comment_time' => $cur_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_buyer_comment=0 AND status IN (". self::STATUS_SUCCESS .")");
		if( !$ret )
		{
			$result['result'] = -5;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '����',
			'process_result' => '������',
			'process_content' => '{buyer_nickname} �������� {seller_nickname}',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_class')->comment_order_for_buyer_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * �������۶���
	 * @param int $order_id
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function comment_order_for_seller($order_id, $user_id, $is_anonymous)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_id = intval($order_id);
		$user_id = intval($user_id);
		$is_anonymous = intval($is_anonymous);
		if( strlen($order_id)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status!==self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//����״̬
		$data = array(
			'is_seller_comment' => 1,
			'seller_comment_time' => $cur_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_seller_comment=0 AND status IN (". self::STATUS_SUCCESS .")");
		if( !$ret )
		{
			$result['result'] = -5;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '����',
			'process_result' => '������',
			'process_content' => '{seller_nickname} �������� {buyer_nickname}',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_class')->comment_order_for_seller_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * ���ɾ������
	 * @param string $order_sn
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function del_order_for_buyer($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id>0 && $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����״̬
		$data = array(
			'is_buyer_del' => 1,
			'buyer_del_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_buyer_del=0 AND status IN (". self::STATUS_CLOSED .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = 'ɾ���ɹ�';
		return $result;
	}
	
	/**
	 * ����ɾ������
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function del_order_for_seller($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		
		//��鶩��
		if( $user_id>0 && $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����״̬
		$data = array(
			'is_seller_del' => 1,
			'seller_del_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_seller_del=0 AND status IN (". self::STATUS_CLOSED .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = 'ɾ���ɹ�';
		return $result;
	}


	/**
	 * ��ȡĳ��ʱ�������ڱ��ܾ��Ķ���������detail��
	 * @param  int   $type_id        ����id
	 * @param  int 	 $user_id		 ����id
	 * @param  int	 $start_time 	 ��ʼ��ѯʱ��
	 * @param  int	 $end_time		 ������ѯʱ��
	 * @return array
	 */
	public function get_order_refuse_by_lasting($type_id, $user_id, $start_time, $end_time)
	{
		return $this->order_detail_obj->get_order_refuse_by_lasting($type_id, $user_id, $start_time, $end_time);
	}

	/**
	 * ��ȡĳ��ʱ����������Ʒ���۵�����
	 * @param  int   $type_id        ����id
	 * @param  int	 $start_time 	 ��ʼ��ѯʱ��
	 * @param  int	 $end_time		 ������ѯʱ��
	 * @param  int   $limit 		 ��ѯ����
	 * @return array
	 */
	public function get_goods_sales_ranking($type_id, $start_time, $end_time, $limit='0,9999999999')
	{

		return $this->order_detail_obj->get_goods_sales_ranking($type_id, $start_time, $end_time, $limit);
	}

	/**
	 * �Ƿ��ǩ������
	 * @param int $order_id
	 * @return boolean
	 */
	public function is_wait_sign_order($order_id)
	{
		//������
		$order_id = intval($order_id);
		if( strlen($order_id)<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			return false;
		}
		$status = intval($order_info['status']);
		
		//�ж϶���״̬
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			return false;
		}
		return true;
	}
}
