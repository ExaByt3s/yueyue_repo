<?php
/**
 * ������
 * 
 * @author
 */

class pai_mall_order_class extends POCO_TDG
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
	 * ���񶩵�
	 * @var null|object
	 */
	private $order_detail_obj = NULL;

	/**
	 * �����
	 * @var null|object
	 */
	private $order_activity_obj = NULL;

	/**
	 * �����
	 * @var null|object
	 */
	private $order_payment_obj = NULL;

	/**
	 * ���Է�֧�û��б�
	 * @var array
	 */
	private $test_users = array(116127,117452,100049);

	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
		$this->order_detail_obj = POCO::singleton('pai_mall_order_detail_class');
		$this->order_activity_obj = POCO::singleton('pai_mall_order_activity_class');
		$this->order_payment_obj = POCO::singleton('pai_mall_order_payment_class');
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
	private function set_mall_order_detail_tbl()
	{
		$this->setTableName('mall_order_detail_tbl');
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
	 * ��Ӷ�����ϸ
	 * @param array $data
	 * @return int
	 */
	private function add_order_detail($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_detail_tbl();
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
	 * �޸Ķ�����ϸ��Ϣ��detail��
	 * @param array $data
	 * @param string $where_str
	 * @return boolean
	 */
	private function update_order_detail_by_where($data, $where_str)
	{
		$where_str = trim($where_str);
		if( !is_array($data) || empty($data) || strlen($where_str)<1 )
		{
			return false;
		}
		$this->set_mall_order_detail_tbl();
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
		$order_type = trim($order_info['order_type']);

		$order_full_list = array();
		if( $order_type=='detail' )
		{
			$order_full_list = $this->fill_order_full_list_for_detail(array($order_info), $login_user_id);
		}
		elseif( $order_type=='activity' )
		{
			$order_full_list = $this->fill_order_full_list_for_activity(array($order_info), $login_user_id);
		}
		elseif( $order_type=='payment' )
		{
			$order_full_list = $this->fill_order_full_list_for_payment(array($order_info), $login_user_id);
		}
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
		$order_type = trim($order_info['order_type']);
		$order_full_list = array();
		if( $order_type=='detail' )
		{
			$order_full_list = $this->fill_order_full_list_for_detail(array($order_info), $login_user_id);
		}
		elseif( $order_type=='activity' )
		{
			$order_full_list = $this->fill_order_full_list_for_activity(array($order_info), $login_user_id);
		}
		elseif( $order_type=='payment' )
		{
			$order_full_list = $this->fill_order_full_list_for_payment(array($order_info), $login_user_id);
		}
		$order_full_info = $order_full_list[0];
		if( !is_array($order_full_info) ) $order_full_info = array();
		return $order_full_info;
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
	 * @param string $order_type ��������
	 * @return array|int
	 */
	public function get_order_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $order_type='')
	{
		$type_id = intval($type_id);
		$status = intval($status);
		$order_type = trim($order_type);
		
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
		if( strlen($order_type)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_type='{$order_type}'";
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
	 * ��ȡ�����б�
	 * @param int $type_id ��ƷƷ��id
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_order_list_for_detail($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_type = 'detail';
		return $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields, $order_type);
	}

	/**
	 * ��ȡ��б�
	 * @param int $type_id ��ƷƷ��id
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_order_list_for_activity($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_type = 'activity';
		return $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields, $order_type);
	}

	/**
	 * ��������Ӳ�ѯ�����Ķ����б�
	 * @param int $type_id ��ƷƷ��ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $where_str ��ѯ���
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param string $order_type ��������
	 * @return array
	 */
	public function get_order_full_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $order_type='')
	{
		$ret = $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields, $order_type);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
	}

	/**
	 * ��������Ӳ�ѯ�����Ķ����б�
	 * @param int $type_id ��ƷƷ��ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $where_str ��ѯ���
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param string $order_type ��������
	 * @return array
	 */
	public function get_order_full_list_for_detail($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $order_type='')
	{
		$ret = $this->get_order_list_for_detail($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list_for_detail($ret);
	}

	/**
	 * ��������Ӳ�ѯ�����Ķ����б�
	 * @param int $type_id ��ƷƷ��ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $where_str ��ѯ���
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @return array
	 */
	public function get_order_full_list_for_activity($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$ret = $this->get_order_list_for_activity($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list_for_activity($ret);
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
	    return $rst;
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
	public function get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_buyer_comment=-1, $order_type='')
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_buyer_comment = intval($is_buyer_comment);
		$order_type = trim($order_type);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}
		//�����ѯ����
		$sql_where = " buyer_user_id={$user_id} AND is_buyer_del=0";
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

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields, $order_type);
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
	 * ��ȡ��Ҷ����б�
	 * @param int $user_id ����û�ID
	 * @param int $type_id ��ƷƷ��ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_buyer_comment [����Ƿ�������]
	 * @param string $order_type �������� ���� detail��� activity�����渶 payment
	 * @return array
	 */
	public function get_order_list_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1, $order_type='')
	{
		return $this->get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 1, $is_buyer_comment, $order_type);
	}

	/**
	 * ��ȡ��ҷ��񶩵��б�
	 * @param int $user_id ����û�ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_buyer_comment [����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_by_detail_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'detail');
		return $this->fill_order_full_list_for_detail($rst);
	}

	/**
	 * ��ȡ��һ�����б�
	 * @param int $user_id ����û�ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_buyer_comment [����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_by_activity_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'activity');
		return $this->fill_order_full_list_for_activity($rst);
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
	public function get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_seller_comment=-1, $order_type)
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_seller_comment = intval($is_seller_comment);
		$order_type = trim($order_type);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//�����ѯ����
		$sql_where = "seller_user_id={$user_id} AND is_seller_del=0";
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

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields, $order_type);
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
	 * @param string $order_type �������� ���� detail��� activity�����渶 payment
	 * @return array
	 */
	public function get_order_list_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_seller_comment=-1, $order_type='')
	{
		return $this->get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 1, $is_seller_comment, $order_type);
	}

	/**
	 * ��ȡ���ҷ��񶩵��б�
	 * @param int $user_id ����û�ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_buyer_comment [����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_by_detail_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'detail');
		return $this->fill_order_full_list_for_detail($rst);
	}


	/**
	 * ��ȡ���һ�����б�
	 * @param int $user_id ����û�ID
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_buyer_comment [����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_by_activity_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'activity');
		return $this->fill_order_full_list_for_activity($rst);
	}

	/**
	 * ��ȡ����ζ���
	 * @param int $user_id �̼�ID
	 * @param int $type_id
	 * @param int $status
	 * @return mixed
	 * @throws App_Exception
	 */
	public function get_activity_list_by_order_for_seller($user_id, $status, $b_select_count, $order_by, $limit)
	{
		return $this->order_activity_obj->get_activity_list_by_order_for_seller($user_id, $status, $b_select_count, $order_by, $limit);
	}

	/**
	 * ��ȡ����г�����֧����������ȡ��������
	 * @param $activity_id
	 * @param $stage_id
	 */
	public function sum_order_quantity_of_paid_by_activity($activity_id)
	{
		return $this->order_activity_obj->sum_order_quantity_of_paid_by_activity($activity_id);
	}

	/**
	 * ��ȡ������֧����������ȡ��������
	 * @param $activity_id
	 * @param $stage_id
	 */
	public function sum_order_quantity_of_paid_by_stage($activity_id, $stage_id)
	{
		return $this->order_activity_obj->sum_order_quantity_of_paid_by_stage($activity_id, $stage_id);
	}

	/**
	 * ��ȡ����ζ���
	 * @param int $activity_id �ID
	 * @param int $stage_id ����ID
	 * @param int $status ״̬ 0 �����2 ��ǩ����7 �ѹرգ�8 ��֧��
	 * @param bool $b_select_count �Ƿ��ѯ������Ŀ
	 * @param string $where_str ��ѯ���
	 * @param string $order_by ����
	 * @param string $limit ����
	 */
	public function get_order_list_by_activity_stage($activity_id, $stage_id, $status, $b_select_count, $where_str='', $order_by, $limit)
	{
		return $this->order_activity_obj->get_order_list_by_activity_stage($activity_id, $stage_id, $status, $b_select_count, $where_str, $order_by, $limit);
	}

	/**
	 * ���ݻ����ID��ȡ��֧�������б�������������
	 * @param $activity_id
	 * @param $stage_id
	 * @param bool $b_select_count
	 * @param string $order_by
	 * @param string $limit
	 * @return array|mixed
	 */
	public function get_order_list_of_paid_by_stage($activity_id, $stage_id, $b_select_count=false, $order_by='', $limit='0,20')
	{
		return $this->order_activity_obj->get_order_list_of_paid_by_stage($activity_id, $stage_id, $b_select_count, $order_by, $limit);
	}

	/**
	 * ���ݻID��ȡĳ������Ƿ���ĳ��״̬�ĵ�
	 * @param $user_id
	 * @param $activity_id
	 * @param bool $b_select_count
	 * @param int $is_fill_order
	 * @return array|mixed
	 * @throws App_Exception
	 */
	public function get_order_list_by_activity_id_for_buyer($user_id, $activity_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $is_fill_order=0)
	{
		return $this->order_activity_obj->get_order_list_by_activity_id_for_buyer($user_id, $activity_id, $status, $b_select_count, $order_by, $limit, $is_fill_order);
	}

	/**
	 * ��ȡ��Ҷ���������Ŀ����
	 * @param int $user_id ����û�ID
	 * @param string|array $order_type array('detail', 'activity')
	 * @return array
	 */
	public function get_order_number_for_buyer($user_id, $order_type='')
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//�������
		$order_type_str = '';
		if( !is_array($order_type) )
		{
			$order_type = trim($order_type);
			if( strlen($order_type)>0 )
			{
				$order_type = array($order_type);
			}
			else
			{
				$order_type = array();
			}
		}
		if( !empty($order_type) )
		{
			$order_type_str = "'". implode("','", $order_type) . "'";
		}
		
		$fields = ' status,COUNT(*) as c ';
		$where_str = " buyer_user_id={$user_id} AND is_seller_del=0";
		if( strlen($order_type_str)>0 )
		{
			$where_str.= " AND order_type IN ({$order_type_str})";
		}
		$where_str.= " GROUP BY status";
		$ret = $this->get_order_list(0, -1, false, $where_str, '', '0,99999999', $fields);

		//��ȡ������״̬
		$comment_where_str = " buyer_user_id={$user_id} AND is_seller_del=0 AND is_buyer_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

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
	 * ��ȡ��ҷ��񶩵�������Ŀ����
	 * @param int $user_id ����û�ID
	 * @return array
	 */
	public function get_order_number_by_detail_for_buyer($user_id)
	{
		$order_type = 'detail';
		return $this->get_order_number_for_buyer($user_id, $order_type);
	}

	/**
	 * ��ȡ��һ����������Ŀ����
	 * @param int $user_id ����û�ID
	 * @return array
	 */
	public function get_order_number_by_activity_for_buyer($user_id)
	{
		$order_type = 'activity';
		$rst = $this->get_order_number_for_buyer($user_id, $order_type);
		unset($rst['wait_confirm']);
		return $rst;
	}

	/**
	 * ��ȡ���Ҷ���������Ŀ����
	 * @param int $user_id �����û�ID
	 * @param string|array $order_type array('detail', 'activity')
	 * @param string $where_str
	 * @return array
	 */
	public function get_order_number_for_seller($user_id, $order_type='', $where_str='')
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//�������
		$order_type_str = '';
		if( !is_array($order_type) )
		{
			$order_type = trim($order_type);
			if( strlen($order_type)>0 )
			{
				$order_type = array($order_type);
			}
			else
			{
				$order_type = array();
			}
		}
		if( !empty($order_type) )
		{
			$order_type_str = "'". implode("','", $order_type) . "'";
		}
		
		$fields = ' status,COUNT(*) as c ';
		$sql_where = " seller_user_id={$user_id} AND is_seller_del=0";
		if( strlen($order_type_str)>0 )
		{
			$sql_where.= " AND order_type IN ({$order_type_str})";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		$sql_where.= " GROUP BY status";
		$ret = $this->get_order_list(0, -1, false, $sql_where, '', '0,99999999', $fields);

		//��ȡ������״̬
		$comment_where_str = " seller_user_id={$user_id} AND is_seller_del=0 AND is_seller_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

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
	 * ��ȡ���ҷ��񶩵�������Ŀ����
	 * @param int $user_id ����û�ID
	 * @return array
	 */
	public function get_order_number_by_detail_for_seller($user_id)
	{
		$order_type = 'detail';
		return $this->get_order_number_for_seller($user_id, $order_type);
	}

	/**
	 * ��ȡ���һ����������Ŀ����
	 * @param int $user_id ����û�ID
	 * @return array
	 */
	public function get_order_number_by_activity_for_seller($user_id)
	{
		$order_type = 'activity';
		$rst = $this->get_order_number_for_seller($user_id, $order_type);
		unset($rst['wait_confirm']);
		return $rst;
	}

	/**
	 * ��ȡ���ҳ��ζ���������Ŀ����
	 * @param int $user_id �����û�ID
	 * @param int $activity_id �ID
	 * @param int $stage_id ����ID
	 * @return array
	 */
	public function get_order_number_by_stage_for_seller($user_id, $activity_id, $stage_id)
	{
		return $this->order_activity_obj->get_order_number_by_stage_for_seller($user_id, $activity_id, $stage_id);
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

		$code_obj = POCO::singleton('pai_activity_code_class');

		//��ȡϵͳ�ǳơ�ϵͳͷ��
		$sys_nickname = get_user_nickname_by_user_id(10002);
		$sys_icon = get_user_icon(10002, 165);

		foreach($list as $key=>$info)
		{
			$order_type = trim($info['order_type']);
			$info_tmp = array();
			if( $order_type=='detail' )
			{
				$info_tmp = $this->fill_order_full_list_for_detail(array($info));
			}
			elseif( $order_type=='activity' )
			{
				$info_tmp = $this->fill_order_full_list_for_activity(array($info));
			}
			elseif( $order_type=='payment' )
			{
				$info_tmp = $this->fill_order_full_list_for_payment(array($info));
			}
			$list[$key] = $info_tmp[0];
		}
		return $list;
	}

	/**
	 * ���䶩��������Ϣ
	 * @param array $list
	 * @param int $login_user_id ��ǰ��¼��id
	 * @return array
	 */
	private function fill_order_full_list_for_detail($list, $login_user_id=0)
	{
		return $this->order_detail_obj->fill_order_full_list($list, $login_user_id);
	}

	/**
	 * ���䶩��������Ϣ
	 * @param array $list
	 * @param int $login_user_id ��ǰ��¼��id
	 * @return array
	 */
	private function fill_order_full_list_for_activity($list, $login_user_id=0)
	{
		return $this->order_activity_obj->fill_order_full_list($list, $login_user_id);
	}
	
	/**
	 * ���䶩��������Ϣ
	 * @param array $list
	 * @param int $login_user_id ��ǰ��¼��id
	 * @return array
	 */
	private function fill_order_full_list_for_payment($list, $login_user_id=0)
	{
		return $this->order_payment_obj->fill_order_full_list($list, $login_user_id);
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
	 * ��ȡ�б�
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
	 * ��ȡ�б�
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
	 * �ύ����
	 * @param int $buyer_user_id ����û�ID
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
	public function submit_order($buyer_user_id, $detail_list, $more_info=array())
	{
		return $this->order_detail_obj->submit_order($buyer_user_id, $detail_list, $more_info);
	}

	/**
	 * �ύ�����
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

		/*
		//��ȡ������ϸ��Ϣ
		$detail_list = $this->get_detail_list_all($order_id);
		if( !is_array($detail_list) || count($detail_list)!=1 )
		{
			$result['result'] = -4;
			$result['message'] = '��ȡ������ϸ��Ϣ����';
			return $result;
		}
		$order_detail_id = $detail_list[0]['order_detail_id'];
		*/
		
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

		/*
		$detail_data = array(
			'amount' => $change_price,
			'is_change_price' => $is_change_price,
		);
		$detail_ret = $this->update_order_detail_by_where($detail_data,"order_detail_id={$order_detail_id}");
		if( !$detail_ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -13;
			$result['message'] = '������ϸ�ļ�ʧ��';
			return $result;
		}
		*/

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->change_order_price_after($trigger_params);

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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->cal_pay_order_for_detail($order_sn, $user_id, $is_available_balance, $coupon_sn);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->cal_pay_order_for_activity($order_sn, $user_id, $is_available_balance, $coupon_sn);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->cal_pay_order_for_payment($order_sn, $user_id, $is_available_balance, $coupon_sn);
		}

		return $result;
	}

	/**
	 * �������֧��ҳ����
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param $coupon_sn
	 * @return mixed
	 */
	public function cal_pay_order_for_detail($order_sn, $user_id, $is_available_balance, $coupon_sn)
	{
		return $this->order_detail_obj->cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn);
	}

	/**
	 * ����֧��ҳ����
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param $coupon_sn
	 * @return mixed
	 */
	public function cal_pay_order_for_activity($order_sn, $user_id, $is_available_balance, $coupon_sn)
	{
		return $this->order_activity_obj->cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn);
	}

	/**
	 * ����֧��ҳ����
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param $coupon_sn
	 * @return mixed
	 */
	public function cal_pay_order_for_payment($order_sn, $user_id, $is_available_balance, $coupon_sn)
	{
		return $this->order_payment_obj->cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn);
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
		$order_full_info = $this->get_order_full_info($order_sn);
		if( $user_id!=$order_full_info['buyer_user_id'] )
		{
			return array();
		}
		$order_id = intval($order_full_info['order_id']);
        $mall_order_type = trim($order_full_info['order_type']);
		$mall_type_id = intval($order_full_info['type_id']);
		$seller_user_id = intval($order_full_info['seller_user_id']);
		$org_user_id = intval($order_full_info['org_user_id']);
		$order_total_amount = trim($order_full_info['total_amount']);

        $mall_goods_id = 0;
        $mall_stage_id = 0;
        if($mall_order_type=='detail')
        {
            $mall_goods_id = intval($order_full_info['detail_list'][0]['goods_id']);
        }
        elseif($mall_order_type=='activity')
        {
            $mall_goods_id = intval($order_full_info['activity_list'][0]['activity_id']);
            $mall_stage_id = intval($order_full_info['activity_list'][0]['stage_id']);
        }
		
		$param_info = array(
			'channel_module' => $this->channel_module,
			'channel_oid' => $order_id,
            'mall_order_type' => $mall_order_type, 
			'module_type' => $this->channel_module, // yuepai waipai
			'order_total_amount' => $order_total_amount, // �����ܶ�
			'model_user_id' => $seller_user_id, // ģ���û�ID�����ݾ�Լ��ȯ������
			'event_user_id' => $seller_user_id, // ��֯���û�ID���������ĵ�����
			'event_id' => $mall_goods_id, // �ID���������ĵ�����
			'org_user_id' => $org_user_id, // ����ID
			'mall_type_id' => $mall_type_id, //����Ʒ��
			'seller_user_id' => $seller_user_id, //�����û�ID
			'mall_goods_id' => $mall_goods_id, //��ƷID
			'mall_stage_id' => $mall_stage_id, //�����ID
		);
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
		$order_type= trim($order_info['order_type']);

		//��ȡ������ϸ�б�
		if( $order_type=='detail' )
		{
			$result = $this->submit_pay_order_for_detail($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->submit_pay_order_for_activity($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->submit_pay_order_for_payment($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
		}
		return $result;
	}

	/**
	 * �ύ����֧��
	 * @param $order_sn
	 * @param $user_id
	 * @param $available_balance
	 * @param $is_available_balance
	 * @param $third_code
	 * @param $redirect_url
	 * @param $notify_url
	 * @param $coupon_sn
	 * @param $more_info
	 * @return mixed
	 */
	public function submit_pay_order_for_detail($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info)
	{
		return $this->order_detail_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
	}

	/**
	 * �ύ�֧��
	 * @param $order_sn
	 * @param $user_id
	 * @param $available_balance
	 * @param $is_available_balance
	 * @param $third_code
	 * @param $redirect_url
	 * @param $notify_url
	 * @param $coupon_sn
	 * @param $more_info
	 * @return mixed
	 */
	public function submit_pay_order_for_activity($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info)
	{
		return $this->order_activity_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
	}

	/**
	 * �ύ���渶֧��
	 * @param $order_sn
	 * @param $user_id
	 * @param $available_balance
	 * @param $is_available_balance
	 * @param $third_code
	 * @param $redirect_url
	 * @param $notify_url
	 * @param $coupon_sn
	 * @param $more_info
	 * @return mixed
	 */
	public function submit_pay_order_for_payment($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info)
	{
		return $this->order_payment_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
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
		$channel_param = trim($payment_info['channel_param']);

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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->pay_order_by_payment_info_for_detail($payment_info);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->pay_order_by_payment_info_for_activity($payment_info);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->pay_order_by_payment_info_for_payment($payment_info);
		}
		return $result;
	}

	/**
	 * ֧�����񶩵�������֧����Ϣ
	 * @param $payment_info
	 * @return mixed
	 */
	public function pay_order_by_payment_info_for_detail($payment_info)
	{
		return $this->order_detail_obj->pay_order_by_payment_info($payment_info);
	}

	/**
	 * ֧�������������֧����Ϣ
	 * @param $payment_info
	 * @return mixed
	 */
	public function pay_order_by_payment_info_for_activity($payment_info)
	{
		return $this->order_activity_obj->pay_order_by_payment_info($payment_info);
	}

	/**
	 * ֧�������������֧����Ϣ
	 * @param $payment_info
	 * @return mixed
	 */
	public function pay_order_by_payment_info_for_payment($payment_info)
	{
		return $this->order_payment_obj->pay_order_by_payment_info($payment_info);
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
		return $this->order_detail_obj->refuse_order($order_sn, $user_id);
	}

	/**
	 * �رճ��������ж���
	 * @param int $activity_id
	 * @param int $stage_id
	 * @return array
	 */
	public function close_order_for_stage($activity_id, $stage_id)
	{
		return $this->order_activity_obj->close_order_for_stage($activity_id, $stage_id);
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
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->close_order_for_seller_by_detail($order_sn,$user_id);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->close_order_for_seller_by_activity($order_sn,$user_id);
		}

		return $result;
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_seller_by_detail($order_sn,$user_id)
	{
		return $this->order_detail_obj->close_order_for_seller($order_sn,$user_id);
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_seller_by_activity($order_sn,$user_id)
	{
		return $this->order_activity_obj->close_order_for_seller($order_sn,$user_id);
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
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->close_order_for_buyer_by_detail($order_sn,$user_id);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->close_order_for_buyer_by_activity($order_sn,$user_id);
		}

		return $result;
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_buyer_by_detail($order_sn,$user_id)
	{
		return $this->order_detail_obj->close_order_for_buyer($order_sn,$user_id);
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_buyer_by_activity($order_sn,$user_id)
	{
		return $this->order_activity_obj->close_order_for_buyer($order_sn,$user_id);
	}
	
	/**
	 * ��������˿�
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->refund_order_for_buyer_by_detail($order_sn,$user_id);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->refund_order_for_buyer_by_activity($order_sn,$user_id);
		}

		return $result;
	}

	/**
	 * �����������˿�
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer_by_detail($order_sn, $user_id)
	{
		return $this->order_detail_obj->refund_order_for_buyer($order_sn,$user_id);
	}

	/**
	 * ��������˿�
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer_by_activity($order_sn, $user_id)
	{
		return $this->order_activity_obj->refund_order_for_buyer($order_sn,$user_id);
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

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->close_order_for_system_by_detail($order_sn,$reason);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->close_order_for_system_by_activity($order_sn,$reason);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->close_order_for_system_by_payment($order_sn, $reason);
		}

		return $result;
	}

	/**
	 * ϵͳ�رշ��񶩵�
	 * @param $order_sn
	 * @param $reason
	 * @return mixed
	 */
	public function close_order_for_system_by_detail($order_sn,$reason)
	{
		return $this->order_detail_obj->close_order_for_system($order_sn,$reason);
	}

	/**
	 * ϵͳ�رջ����
	 * @param $order_sn
	 * @param $reason
	 * @return mixed
	 */
	public function close_order_for_system_by_activity($order_sn,$reason)
	{
		return $this->order_activity_obj->close_order_for_system($order_sn,$reason);
	}
	
	/**
	 * ϵͳ�ر��渶����
	 * @param $order_sn
	 * @param $reason
	 * @return mixed
	 */
	public function close_order_for_system_by_payment($order_sn, $reason)
	{
		return $this->order_payment_obj->close_order_for_system($order_sn);
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

		//��ȡǩ������Ϣ
		$code_info = $this->get_code_info_recently($code_sn);
		if( empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ�������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$order_id = intval($code_info['order_id']);

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
		$order_type =trim($order_info['order_type']);

		if( $order_type=='activity' )
		{
			$result = $this->sign_order_for_activity($code_sn, $user_id);
		}
		elseif( $order_type=='detail' )
		{
			$result = $this->sign_order_for_detail($code_sn, $user_id);
		}

		return $result;
	}

	/**
	 * ǩ������
	 * �����������ǩ������ҳ�ʾǩ���룬���ҵ���ɨ�뾵ͷ��
	 * @param string $code_sn ǩ����
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order_for_detail($code_sn, $user_id)
	{
		return $this->order_detail_obj->sign_order($code_sn, $user_id);
	}

	/**
	 * ǩ������
	 * �����������ǩ������ҳ�ʾǩ���룬���ҵ���ɨ�뾵ͷ��
	 * @param string $code_sn ǩ����
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order_for_activity($code_sn, $user_id)
	{
		return $this->order_activity_obj->sign_order($code_sn, $user_id);
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
		$order_type =trim($order_info['order_type']);

		if( $order_type=='activity' )
		{
			$result = $this->sign_order_for_system_by_activity($order_sn, $is_expired);
		}
		elseif( $order_type=='detail' )
		{
			$result = $this->sign_order_for_system_by_detail($order_sn, $is_expired);
		}

		return $result;
	}

	/**
	 * @param $order_sn
	 * @param $is_expired
	 * @return mixed
	 */
	public function sign_order_for_system_by_activity($order_sn, $is_expired)
	{
		return $this->order_activity_obj->sign_order_for_system($order_sn, $is_expired);
	}

	/**
	 * @param $order_sn
	 * @param $is_expired
	 * @return mixed
	 */
	public function sign_order_for_system_by_detail($order_sn, $is_expired)
	{
		return $this->order_detail_obj->sign_order_for_system($order_sn, $is_expired);
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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->order_detail_obj->comment_order_for_buyer($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->order_activity_obj->comment_order_for_buyer($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->order_payment_obj->comment_order_for_buyer($order_id, $user_id, $is_anonymous);
		}
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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->order_detail_obj->comment_order_for_seller($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->order_activity_obj->comment_order_for_seller($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->order_payment_obj->comment_order_for_seller($order_id, $user_id, $is_anonymous);
		}
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
	 * ��ȡĳ��ʱ�������ڱ��ܾ��Ķ�������
	 * @param  int   $type_id        ����id
	 * @param  int 	 $user_id		 ����id
	 * @param  int	 $start_time 	 ��ʼ��ѯʱ��
	 * @param  int	 $end_time		 ������ѯʱ��
	 * @return array
	 */
	public function get_order_refuse_by_lasting($type_id, $user_id, $start_time, $end_time)
	{
		$type_id = intval($type_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);

		$fields = ' d.goods_id,COUNT(d.goods_id) AS count ';

		//�����ѯ����
		$sql_where = ' WHERE 1';
		if( $type_id>0 )
		{
			$sql_where .= " AND o.type_id={$type_id}";
		}
		if( $user_id>0 )
		{
			$sql_where .= " AND o.seller_user_id={$user_id} ";
		}
		if( $start_time>0 )
		{
			$sql_where .= " AND o.close_time>={$start_time} ";
		}
		if( $end_time>0 )
		{
			$sql_where .= " AND o.close_time<={$end_time} ";
		}

		$where_str = " AND o.close_by='seller' AND o.close_status=1 AND o.status=7 ";

		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where}"
			. " GROUP BY d.goods_id";
		$ret = $this->query($sql);

		return $ret;
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
		$type_id = intval($type_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);

		$fields = ' d.goods_id,COUNT(d.goods_id) AS sales_count ';

		//�����ѯ����
		$sql_where = ' WHERE 1';
		if( $type_id>0 )
		{
			$sql_where .= " AND o.type_id={$type_id}";
		}
		if( $start_time>0 )
		{
			$sql_where .= " AND o.add_time>={$start_time} ";
		}
		if( $end_time>0 )
		{
			$sql_where .= " AND o.add_time<={$end_time} ";
		}

		$where_str = " AND o.status=8 ";

		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where}"
			. " GROUP BY d.goods_id"
			. " ORDER BY sales_count DESC"
			. " LIMIT {$limit}";
		$ret = $this->query($sql);

		return $ret;
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
	
	/**
	 * ����ǩ����
	 * @param int $buyer_user_id
	 * @param int $seller_user_id
	 * @param int $order_id
	 * @param int $order_detail_id
	 * @return string
	 */
	private function generate_code_sn($buyer_user_id, $seller_user_id, $order_id, $order_detail_id)
	{
		//������
		$buyer_user_id = intval($buyer_user_id);
		$seller_user_id = intval($seller_user_id);
		$order_id = intval($order_id);
		$order_detail_id = intval($order_detail_id);
		if( $buyer_user_id<1 || $seller_user_id<1 || $order_id<1 || $order_detail_id<1 )
		{
			return '';
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		$code_sn = '';
		$while_count = 0;
		while($while_count<9999)
		{
			//��ȡ����ַ���
			$rand_str = $this->get_rand_str(6);
			if( strlen($rand_str)<1 )
			{
				//ǩ����Ϊ��
				break;
			}
			
			//����Ƿ��ܸ���
			$c = $this->check_code_recently($rand_str);
			if( $c>0 )
			{
				$while_count++;
				continue;
			}
			
			//����
			$data = array(
				'buyer_user_id' => $buyer_user_id,
				'seller_user_id' => $seller_user_id,
				'order_id' => $order_id,
				'order_detail_id' => $order_detail_id,
				'code_sn' => $rand_str,
				'add_time' => time(),
			);
			$id = $this->add_code($data);
			if( $id<1 )
			{
				$while_count++;
				continue;
			}
			
			//�ٴμ���Ƿ��ܸ���
			$c = $this->check_code_recently($rand_str);
			if( $c>1 )
			{
				//��������һ������Ҳ������
				$while_count++;
				continue;
			}
			
			$code_sn = $rand_str;
			break;
		}
		if( strlen($code_sn)<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			return '';
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		return $code_sn;
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
		
		//��1λ������1��3��5��7��9����������������ϵͳ
		$pattern_str = '31597';
		$pattern_len = strlen($pattern_str);
		$code .= substr($pattern_str, rand(0, $pattern_len-1), 1);
		
		//��λ
		$length--;
		$pattern_str = '8029753416';
		$pattern_len = strlen($pattern_str);
		for($i=0; $i<$length; $i++)
		{
			$code .= substr($pattern_str, rand(0, $pattern_len-1), 1);
		}
		
		return $code;
	}

	public function get_order_pay_num($goods_id)
	{
		$sql_where = " WHERE d.goods_id={$goods_id} AND is_pay=1 AND status!=7 ";
		$sql = "SELECT count(*) as total "
			. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where}";
		$rs = $this->query($sql);
		return $rs[0];
	}
}
