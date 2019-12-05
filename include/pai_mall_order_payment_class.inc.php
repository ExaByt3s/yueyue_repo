<?php
/**
 * �������
 * 
 * @author Henry
 * @copyright 2015-10-29
 */

class pai_mall_order_payment_class extends POCO_TDG
{
	/**
	 * ����ģ��
	 * @var string
	 */
	private $channel_module = 'mall_order';
	
	/**
	 * ��������
	 * @var string
	 */
	private $order_type = 'payment';
	
	/**
	 * ��֧��
	 * ���������µ����ȴ�������֧��
	 * @var int
	 */
	const STATUS_WAIT_PAY = 0;
	
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
	 * ״̬����
	 * @var array
	 */
	private $status_str_arr = array(
		0 => '��֧��',
		7 => '�ѹر�',
		8 => '�����',
	);
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
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
	private function set_mall_order_payment_tbl()
	{
		$this->setTableName('mall_order_payment_tbl');
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
		$affected_rows = $this->update($data, "order_id={$order_id} AND order_type='{$this->order_type}'");
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
		return $this->find("order_id={$order_id} AND order_type='{$this->order_type}'");
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
		$where_str = "order_sn=:x_order_sn AND order_type='{$this->order_type}'";
		sqlSetParam($where_str, 'x_order_sn', $order_sn);
		$this->set_mall_order_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_order_list($status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
//		$type_id = intval($type_id);
		$status = intval($status);
		
		//�����ѯ����
		$sql_where = "order_type='{$this->order_type}'";
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
	 * ��Ӷ�������
	 * @param array $data
	 * @return int
	 */
	private function add_payment($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_payment_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ��ȡ����
	 * @param int $order_payment_id
	 * @return array
	 */
	private function get_payment_info($order_payment_id)
	{
		$order_payment_id = intval($order_payment_id);
		if( $order_payment_id<1 )
		{
			return array();
		}
		$this->set_mall_order_payment_tbl();
		return $this->find("order_payment_id={$order_payment_id}");
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
	private function get_payment_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
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
		$this->set_mall_order_payment_tbl();
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
	private function get_payment_list_all($order_id)
	{
		return $this->get_payment_list($order_id, false, '', 'order_payment_id ASC', '0,99999999');
	}
	
	/**
	 * ���䶩��������Ϣ
	 * @param array $list
	 * @param int $login_user_id ��ǰ��¼��id
	 * @return array
	 */
	public function fill_order_full_list($list, $login_user_id=0)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		$login_user_id = intval($login_user_id);
		$cur_time = time(); //��ǰʱ��
		
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
				$pay_time_str = date('Y-m-d H:i', $info['pay_time']);
			}
			$info['pay_time_str'] = $pay_time_str;
			
			//��ȡ�������
			$info['buyer_name'] = get_user_nickname_by_user_id($info['buyer_user_id']);
			$info['buyer_icon'] = get_user_icon($info['buyer_user_id'], 165);
			
			//��ȡ��������
			$info['seller_name'] = get_seller_nickname_by_user_id($info['seller_user_id']);
			$info['seller_icon'] = get_seller_user_icon($info['seller_user_id'], 165);
			
			//�����б�
			$payment_list = $this->get_payment_list_all($info['order_id']);
			$info['payment_list'] = $payment_list;
			
			$list[$key] = $info;
		}
		return $list;
	}
	
	/**
	 * ��ȡ������Ϣ
	 * @param string $order_sn ������
	 * @param int $login_user_id
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
	 * ͨ������ID��ȡ������Ϣ
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
	
	/**
	 * ��ȡ��Ҷ����б�
	 * @param int $user_id ����û�ID
	 * @param int $status ����״̬��-1ȫ����0��֧����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $where_str
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_buyer_comtent [����Ƿ�������]
	 * @return array
	 */
	public function get_order_list_for_buyer($user_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$user_id = intval($user_id);
		$where_str = trim($where_str);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}
		
		//�����ѯ����
		$sql_where = "buyer_user_id={$user_id} AND is_buyer_del=0";
		if( $is_buyer_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_buyer_comment={$is_buyer_comment}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		$rst = $this->get_order_list($status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $rst;
		}
		return $this->fill_order_full_list($rst);
	}
	
	/**
	 * ��ȡ�̼Ҷ����б�
	 * @param int $user_id �̼��û�ID
	 * @param int $status ����״̬��-1ȫ����0��֧����7�ѹرգ�8�����
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $where_str
	 * @param string $order_by ����
	 * @param string $limit һ�β�ѯ����
	 * @param string $fields ��ѯ�ֶ�
	 * @param int $is_seller_comment [�̼��Ƿ�������]
	 * @return array
	 */
	public function get_order_list_for_seller($user_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $is_seller_comment=-1)
	{
		$user_id = intval($user_id);
		$where_str = trim($where_str);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}
		
		//�����ѯ����
		$sql_where = "seller_user_id={$user_id} AND is_seller_del=0";
		if( $is_seller_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_seller_comment={$is_seller_comment}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		$rst = $this->get_order_list($status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $rst;
		}
		return $this->fill_order_full_list($rst);
	}
	
	/**
	 * ��ȡ�̼Ҷ����б�����TAB
	 * @param int $user_id �̼��û�ID
	 * @param int $tab today week month all
	 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param string $limit һ�β�ѯ����
	 * @param int $is_seller_comment [�̼��Ƿ�������]
	 * @return array
	 */
	public function get_order_list_by_tab_for_seller($user_id, $tab, $b_select_count=false, $limit='0,20', $is_seller_comment=-1)
	{
		$user_id = intval($user_id);
		$tab = trim($tab);
		if( $user_id<1 || !in_array($tab, array('today', 'week', 'month', 'all'), true) )
		{
			return $b_select_count ? 0 : array();
		}
		if( $tab=='today' )
		{
			//��Ȼ��
			$where_str = 'pay_time>=' . strtotime('today');
		}
		elseif( $tab=='week' )
		{
			//��Ȼ�ܣ�����һ��ʼ
			$where_str = 'pay_time>=' . strtotime("last monday");
		}
		elseif( $tab=='month' )
		{
			//��Ȼ�£�1�տ�ʼ
			$where_str = 'pay_time>=' . strtotime(date('Y-m-01 00:00:00'));
		}
		elseif( $tab=='all' )
		{
			$where_str = '' ;
		}
		return $this->get_order_list_for_seller($user_id, 8, $b_select_count, $where_str , 'pay_time DESC,order_id DESC', $limit, '*', $is_seller_comment);
	}
	
	/**
	 * ��ȡ�̼�ֱ�Ӹ���Ķ�ά��
	 * @param string $url ֱ�Ӹ������ַ��http��ͷ
	 * @param int $seller_user_id �տ����û�ID
	 * @return array
	 */
	public function get_seller_qr_code_info($url, $seller_user_id)
	{
		$result = array('result'=>0, 'message'=>'', 'qr_code_url'=>'');
		
		$url = trim($url);
		$seller_user_id = intval($seller_user_id);
		if( strlen($url)<1 || $seller_user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ�̼���Ϣ
		$seller_obj = POCO::singleton('pai_mall_seller_class');
		$seller_info = $seller_obj->get_seller_info($seller_user_id, 2);
		if( empty($seller_info) )
		{
			$result['result'] = -2;
			$result['message'] = '�̼�Ϊ��';
			return $result;
		}
		
		//���ɶ�ά��ͼƬ����
		if( strpos($url, '?')===false )
		{
			$url .= "?seller_user_id={$seller_user_id}";
		}
		else
		{
			$url .= "&seller_user_id={$seller_user_id}";
		}
		$qr_code_url = pai_activity_code_class::get_qrcode_img($url);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['qr_code_url'] = $qr_code_url;
		return $result;
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
		$not_use_rst = $coupon_obj->not_use_coupon_by_oid($this->channel_module, $order_id);
		if( $not_use_rst['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -3;
			$result['message'] = $not_use_rst['message'];
			return $result;
		}
		
		//����Ϊ0
		$data = array(
			'discount_amount' => 0,
			'is_use_coupon' => 0,
			'coupon_sn' => '',
			'pending_amount' => $total_amount,
		);
		$rst = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=1 AND is_pay=0 AND total_amount={$total_amount}");
		if( !$rst )
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
		$result['used_amount'] = $not_use_rst['used_amount'];
		return $result;
	}
	
	/**
	 * �ύ����
	 * @param int $buyer_user_id �������û�ID
	 * @param double $prime_prices ������
	 * @param int $seller_user_id �̼��û�ID
	 * @param array $more_info ������Ϣ
	 * @return array array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'')
	 * @tutorial
	 * 
	 * $more_info = array(
	 * 	'description' => '', //��������ע
	 *  'referer' => '', //������Դ��app weixin pc wap oa
	 * );
	 * 
	 */
	public function submit_order($buyer_user_id, $prime_prices, $seller_user_id, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'');
		
		//������
		$buyer_user_id = intval($buyer_user_id);
		$prime_prices = number_format($prime_prices*1, 2, '.', '')*1;
		$seller_user_id = intval($seller_user_id);
		if( $buyer_user_id<1 || $prime_prices<=0 || $seller_user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( $prime_prices>5000 )
		{
			$result['result'] = -2;
			$result['message'] = '�ף�����������';
			return $result;
		}
		if( $buyer_user_id==$seller_user_id )
		{
			$result['result'] = -2;
			$result['message'] = '�ף����ܸ��Լ�����Ŷ';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$description = trim($more_info['description']);
		$referer = trim($more_info['referer']);
		
		//��ǰʱ��
		$cur_time = time();
		
		//��ȡƷ������
		$type_id = 20;
		$type_name = '�渶';
		
		//��ȡ�̼���Ϣ
		$seller_obj = POCO::singleton('pai_mall_seller_class');
		$seller_info = $seller_obj->get_seller_info($seller_user_id, 2);
		if( empty($seller_info) )
		{
			$result['result'] = -3;
			$result['message'] = '�̼�Ϊ��';
			return $result;
		}
		$seller_id = intval($seller_info['seller_data']['seller_id']);
		$store_id = intval($seller_info['seller_data']['company'][0]['store'][0]['store_id']);
		
		//�����û�ID
		$relate_org_obj = POCO::singleton('pai_model_relate_org_class');
		$org_info = $relate_org_obj->get_org_info_by_user_id($seller_user_id);
		$org_user_id = intval($org_info['org_id']);
		
		//���㶩�����
		$order_prime_amount = $prime_prices;
		$order_promotion_amount = 0;
		$is_order_promotion = 0;
		$order_promotion_id = 0;
		$order_original_amount = $order_prime_amount - $order_promotion_amount;
		$order_is_change_price = 0;
		$order_total_amount = $order_original_amount;
		
		//����ʱ��
		$expire_time = $cur_time + 3600; //1Сʱ
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//���涩��
		$order_data = array(
			'order_type' => $this->order_type,
			'type_id' => $type_id,
			'type_name' => $type_name,
			'seller_id' => $seller_id,
			'store_id' => $store_id,
			'buyer_user_id' => $buyer_user_id,
			'seller_user_id' => $seller_user_id,
			'org_user_id' => $org_user_id,
			'prime_amount' => $order_prime_amount,
			'order_promotion_amount' => $order_promotion_amount,
			'is_order_promotion' => $is_order_promotion,
			'order_promotion_id' => $order_promotion_id,
			'original_amount' => $order_original_amount,
			'total_amount' => $order_total_amount,
			'discount_amount' => 0,
			'pending_amount' => $order_total_amount,
			'is_change_price' => $order_is_change_price,
			'description' => $description,
			'referer' => $referer,
			'status' => self::STATUS_WAIT_PAY,
			'expire_time' => $expire_time,
			'lately_time'  => $cur_time,
			'add_time' => $cur_time,
		);
		$order_id = $this->add_order($order_data);
		if( $order_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '��������ʧ��';
			return $result;
		}
		$order_sn = rand(10, 99) . $order_id . rand(0, 9);
		$rst = $this->update_order(array('order_sn'=>$order_sn), $order_id);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '����������ʧ��';
			return $result;
		}
		
		//��������
		$payment_data = array(
			'order_id' => $order_id,
		);
		$order_payment_id = $this->add_payment($payment_data);
		if( $order_payment_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '�����ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_payment_class')->submit_order_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_id'] = $order_id;
		$result['order_sn'] = $order_sn;
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
		$total_amount = $order_info['total_amount']*1;
		$cur_amount = $total_amount;
		$status = intval($order_info['status']);

		$payment_list = $this->get_payment_list_all($order_id);
		if( empty($payment_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}

		//��鶩��
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -4;
			$result['message'] = '�Ƿ�����';
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
				$cur_amount = bcsub($cur_amount, $response_data['coupon_amount'],2);
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
		$response_data['pending_amount'] = number_format(bcsub($cur_amount, $response_data['use_balance'], 2), 2, '.', '');

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['response_data'] = $response_data;
		return $result;
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
		$type_id = intval($order_info['type_id']);
		$type_name = trim($order_info['type_name']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$pending_amount = $total_amount;
		$status = intval($order_info['status']);
		
		//��ȡ���и����б�
		$payment_list = $this->get_payment_list_all($order_id);
		if( empty($payment_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		
		//��鶩��
		if( $user_id>0 && $user_id!=$buyer_user_id )
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
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		if( $page_total_amount>0 && $page_total_amount!=$total_amount )
		{
			$result['result'] = -4;
			$result['message'] = '����������';
			return $result;
		}
		
		$cur_time = time(); //��ǰʱ��
		
		//��ȥ����һ�ε��Ż�ȯ
		$not_use_rst = $this->not_use_order_coupon($order_info);
		if( $not_use_rst['result']!=1 )
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
				'mall_order_type' => $this->order_type, //�̳Ƕ�������
				'order_total_amount' => $total_amount, //�����ܽ��
				'org_user_id' => $org_user_id, //����ID,
				'mall_type_id' => $type_id, //��ƷƷ��ID
				'seller_user_id' => $seller_user_id, //�����û�ID
			);
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$coupon_rst = $coupon_obj->use_coupon($buyer_user_id, 1, $coupon_sn, $this->channel_module, $order_id, $param_info);
			if( $coupon_rst['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -5;
				$result['message'] = $coupon_rst['message'];
				return $result;
			}
			$discount_amount = $coupon_rst['used_amount'];
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
			$rst = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=0 AND is_pay=0 AND total_amount={$total_amount}");
			if( !$rst )
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
					'subject' => $type_name,
					'remark' => '',
				);
				$submit_rst = $payment_obj->submit_trade_out_v2($this->channel_module, $order_id, $order_id, $buyer_user_id, $total_amount, $discount_amount, $more_info);
				if( $submit_rst['error']!==0 )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());
					
					$result['result'] = -6;
					$result['message'] = $submit_rst['message'];
					return $result;
				}
				
				//֧������
				$pay_rst = $this->pay_order($order_info);
				if( !$pay_rst )
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
				POCO::singleton('pai_mall_trigger_payment_class')->pay_order_after($trigger_params);
				
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
			$recharge_rst = $payment_obj->submit_recharge($this->channel_module, $buyer_user_id, $amount, $third_code, $order_id, $order_id, 0, $more_info);
			if( $recharge_rst['error']!==0 )
			{
				$result['result'] = -9;
				$result['message'] = $recharge_rst['message'];//��ת��������֧����������  ��ϸ��Ϣ��recharge_ret';
				return $result;
			}
			
			$result['result'] = 1;
			$result['message'] = '����ת��������֧����';
			$result['payment_no'] = trim($recharge_rst['payment_no']);
			$result['request_data'] = trim($recharge_rst['request_data']);
			return $result;
		}
		
		$result['result'] = -10;
		$result['message'] = 'δ֪����';
		return $result;
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
		$type_id = intval($order_info['type_id']);
		$type_name = trim($order_info['type_name']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$is_use_coupon = intval($order_info['is_use_coupon']);
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = $order_info['pending_amount']*1;
		
		//��ȡ���и����б�
		$payment_list = $this->get_payment_list_all($order_id);
		if( empty($payment_list) )
		{
			$result['result'] = -6;
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
			
			$result['result'] = -7;
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
			'subject' => $type_name,
			'remark' => '',
		);
		$payment_obj = POCO::singleton('pai_payment_class');
		$submit_rst = $payment_obj->submit_trade_out_v2($this->channel_module, $order_id, $order_id, $buyer_user_id, $total_amount, $discount_amount, $more_info);
		if( $submit_rst['error']!==0 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -9;
			$result['message'] = $submit_rst['message'];
			return $result;
		}
		
		$pay_rst = $this->pay_order($order_info);
		if( !$pay_rst )
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
		POCO::singleton('pai_mall_trigger_payment_class')->pay_order_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '֧���ɹ�';
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
		
		$cur_time = time(); //��ǰʱ��
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����֧��״̬
		$data = array(
			'is_pay' => 1,
			'pay_time' => $cur_time,
			'is_seller_remind' => 1,
			'status' => self::STATUS_SUCCESS,
			'sign_time' => $cur_time,
			'lately_time' => $cur_time,
		);
		$rst = $this->update_order_by_where($data, "order_id={$order_id} AND is_pay=0 AND status IN (" . self::STATUS_WAIT_PAY .")");
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			return false;
		}
		
		//���㶩��
		$end_rst = $this->end_order($order_id);
		if( $end_rst['result']!=1 )
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
	 * ϵͳ�رն���
	 * ����֧�������˿�
	 * @param string $order_sn
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_system($order_sn)
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
		$is_pay = intval($order_info['is_pay']);
		$status = intval($order_info['status']);
		
		//��鶩��
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
		if( $status!==self::STATUS_WAIT_PAY || $is_pay!=0 )
		{
			$result['result'] = -3;
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
			$result['result'] = -4;
			$result['message'] = $close_ret['message'];
			return $result;
		}
		
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
		
		if( !is_array($more_info) ) $more_info = array();
		$close_by = trim($more_info['close_by']);
		$cur_time = intval($more_info['cur_time']);
		if( $cur_time<1 ) $cur_time = time();
		
		//��鶩��
		if( $status!==self::STATUS_WAIT_PAY || $is_pay!=0 )
		{
			$result['result'] = -2;
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
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND status={$status} AND is_pay=0");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
	
			$result['result'] = -3;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}
		
		//δ�����ʹ���Ż�ȯ
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '��ʹ���Ż�ȯʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
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
		$type_id = intval($order_info['type_id']);
		$type_name = trim($order_info['type_name']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = bcsub($total_amount, $discount_amount, 2);
		
		//��ȡ������ϸ�б�
		$payment_list = $this->get_payment_list_all($order_id);
		if( empty($payment_list) )
		{
			$result['result'] = -6;
			$result['message'] = '������ϸΪ��';
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
			'subject' => $type_name,
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
				'subject' => $type_name,
				'remark' => '',
			);
		}
		
		$refund_list = array();
		$coupon_refund_list = array();
		$payment_obj = POCO::singleton('pai_payment_class');
		$end_rst = $payment_obj->end_event_v2($this->channel_module, $order_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
		if( $end_rst['error']!==0 )
		{
			$result['result'] = -3;
			$result['message'] = '�������ʧ��';
			return $result;
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
		$rst = $this->update_order_by_where($data, "order_id={$order_id} AND is_buyer_comment=0 AND status IN (". self::STATUS_SUCCESS .")");
		if( !$rst )
		{
			$result['result'] = -5;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_payment_class')->comment_order_for_buyer_after($trigger_params);
		
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
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_payment_class')->comment_order_for_seller_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * ��ȡ�̼���������
	 * @param int $seller_user_id
	 * @return int
	 */
	public function sum_seller_remind($seller_user_id)
	{
		$seller_user_id = intval($seller_user_id);
		if( $seller_user_id<1 )
		{
			return 0;
		}
		$where_str = "order_type='{$this->order_type}' AND seller_user_id={$seller_user_id} AND is_seller_remind=1";
		$this->set_mall_order_tbl();
		return $this->findCount($where_str, 'order_id');
	}
	
	/**
	 * ����̼����ѱ�־
	 * @param int $seller_user_id
	 * @param string $order_sn �ձ�ʾ���ȫ��
	 * @return bool
	 */
	public function clear_seller_remind($seller_user_id, $order_sn='')
	{
		$seller_user_id = intval($seller_user_id);
		$order_sn = trim($order_sn);
		if( $seller_user_id<1 )
		{
			return false;
		}
		
		$where_str = "order_type='{$this->order_type}' AND seller_user_id={$seller_user_id}";
		if( strlen($order_sn)>0 )
		{
			$where_str .= " AND order_sn=:x_order_sn";
			sqlSetParam($where_str, 'x_order_sn', $order_sn);
		}
		$data = array(
			'is_seller_remind' => 0,
		);
		$this->update_order_by_where($data, $where_str);
		
		return true;
	}
	
}
