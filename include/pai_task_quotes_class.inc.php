<?php
/**
 * ����
 * @author Henry
 * @copyright 2015-04-09
 */

class pai_task_quotes_class extends POCO_TDG
{
	/**
	 * �ȴ�
	 * @var int
	 */
	const STATUS_WAIT = 0;

	/**
	 * �ѹ�Ӷ
	 * @var int
	 */
	const STATUS_HIRED = 1;

	/**
	 * ��ȡ��
	 * @var int
	 */
	const STATUS_CANCELED = 2;

	/**
	 * һ�㱨������
	 * @var int
	 */
	private $general_quotes_num = 5;

	/**
	 * VIP��������
	 * @var int
	 */
	private $vip_quotes_num = 8;

	/**
	 * �˻����⿨������
	 */
	private $refund_coins_seconds = 172800; //48Сʱ��172800�룬��ʱ20���ӣ�1200��

	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_task_db');
	}

	/**
	 * ָ����
	 */
	private function set_task_quotes_tbl()
	{
		$this->setTableName('task_quotes_tbl');
	}


	/**
	 * ��ȡ����Ȩ��
	 * @param int $user_id
	 * @param int $lead_id
	 * @return true false
	 */
	public function check_user_auth($user_id,$quotes_id)
	{
		$user_id = (int)$user_id;
		$quotes_id = (int)$quotes_id;
		if(!$user_id or !$quotes_id)
		{
			return false;
		}
		$return = $this->get_quotes_info($quotes_id);
		return $return['user_id'] == $user_id?true:false;
	}

	/**
	 * ��ȡ��󱨼�����
	 * @return int
	 */
	public function get_max_quotes_num()
	{
		return max($this->general_quotes_num, $this->vip_quotes_num);
	}

	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_quotes($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_task_quotes_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * �޸�
	 * @param array $data
	 * @param int $quotes_id
	 * @return bool
	 */
	public function update_quotes($data, $quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( !is_array($data) || empty($data) || $quotes_id<1 )
		{
			return false;
		}
		$this->set_task_quotes_tbl();
		$this->update($data, "quotes_id={$quotes_id}");
		return true;
	}

	/**
	 * �޸�
	 * @param array $data
	 * @param int $quotes_id
	 * @return bool
	 */
	private function update_quotes_submit($data, $quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( !is_array($data) || empty($data) || $quotes_id<1 )
		{
			return false;
		}
		$this->set_task_quotes_tbl();
		$this->update($data, "quotes_id={$quotes_id} AND is_pay_coins=0");
		return true;
	}

	/**
	 * ������֧�����⿨
	 * @param int $quotes_id
	 * @param array $more_info array('pay_coins_time'=>0)
	 * @return boolean
	 */
	private function update_quotes_pay_coins($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_pay_coins' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_pay_coins=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * �������˻����⿨
	 * @param int $quotes_id
	 * @param array $more_info array('refund_coins_time'=>0)
	 * @return boolean
	 */
	private function update_quotes_refund_coins($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_refund_coins' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_pay_coins=1 AND is_refund_coins=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * �����Ѳ鿴����
	 * @param int $quotes_id
	 * @param array $more_info array('read_time'=>0)
	 * @return boolean
	 */
	private function update_quotes_read($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_read' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_read=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * �����Ѳ鿴��������
	 * @param int $quotes_id
	 * @param array $more_info array('read_profile_time'=>0)
	 * @return boolean
	 */
	private function update_quotes_read_profile($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_read_profile' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_read_profile=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * ������֧��
	 * @param int $quotes_id
	 * @param array $more_info array('pay_time'=>0, 'payment_no'=>'')
	 * @return boolean
	 */
	private function update_quotes_pay($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_pay' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_pay=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * ����������
	 * @param int $quotes_id
	 * @param array $more_info array('review_time'=>0)
	 * @return boolean
	 */
	private function update_quotes_review($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_review' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_review=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * �����ѹ鵵
	 * @param int $quotes_id
	 * @param array $more_info array('archive_time'=>0)
	 * @return boolean
	 */
	public function update_quotes_archive($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_archive' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_archive=0");
		return $affected_rows>0?true:false;
	}


	/**
	 * ȡ���鵵
	 * @param int $quotes_id
	 * @param array $more_info array('archive_time'=>0)
	 * @return boolean
	 */
	public function update_quotes_cancel_archive($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_archive' => 0,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND is_archive=1");
		return $affected_rows>0?true:false;
	}

	/**
	 * �����Ƿ���Ҫ
	 * @param int $quotes_id
	 * @param int is_important 0�� 1��
	 * @return boolean
	 */
	public function update_quotes_important($quotes_id, $is_important)
	{
		$quotes_id = intval($quotes_id);
		$is_important = intval($is_important);
		if( $quotes_id<1 )
		{
			return false;
		}
		$data = array(
			'is_important' => $is_important,
		);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id}");
		return $affected_rows>0?true:false;
	}

	/**
	 * �����ѹ�Ӷ
	 * @param int $quotes_id
	 * @param array $more_info array('hired_time'=>0)
	 * @return boolean
	 */
	private function update_quotes_status_hired($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => self::STATUS_HIRED,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND status=" . self::STATUS_WAIT);
		return $affected_rows>0?true:false;
	}

	/**
	 * ������ȡ��
	 * @param int $quotes_id
	 * @param array $more_info array('canceled_time'=>0)
	 * @return boolean
	 */
	private function update_quotes_status_canceled($quotes_id, $more_info=array())
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => self::STATUS_CANCELED,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_quotes_tbl();
		$affected_rows = $this->update($data, "quotes_id={$quotes_id} AND status=" . self::STATUS_WAIT);
		return $affected_rows>0?true:false;
	}

	/**
	 * ��ȡ��Ϣ
	 * @param int $quotes_id
	 * @return array
	 */
	public function get_quotes_info($quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return array();
		}
		$this->set_task_quotes_tbl();
		return $this->find("quotes_id={$quotes_id}");
	}

	/**
	 * ��ȡ��Ϣ�������û�ID
	 * @param int $request_id
	 * @param int $user_id
	 * @return array
	 */
	private function get_quotes_info_by_user_id($request_id, $user_id)
	{
		$request_id = intval($request_id);
		$user_id = intval($user_id);
		if( $request_id<1 || $user_id<1 )
		{
			return array();
		}
		$this->set_task_quotes_tbl();
		return $this->find("request_id={$request_id} AND user_id={$user_id}");
	}

	/**
	 * ��ȡ�б�
	 * @param int $request_id
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_quotes_list($request_id, $user_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$request_id = intval($request_id);
		$user_id = intval($user_id);

		//�����ѯ����
		$sql_where = '';

		if( $request_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "request_id={$request_id}";
		}

		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "user_id={$user_id}";
		}

		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//��ѯ
		$this->set_task_quotes_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}

		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡ��Ч�ı����б���������ID
	 * @param int $request_id
	 * @param boolean $b_select_count
	 * @return array
	 */
	public function get_quotes_list_for_valid($request_id, $b_select_count=false)
	{
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			return array();
		}
		$where_str = 'is_pay_coins=1';
		return $this->get_quotes_list($request_id, 0, $b_select_count, $where_str, 'quotes_id ASC', '0,99999999');
	}

	/**
	 * �ύ����
	 * @param int $request_id
	 * @param int $user_id
	 * @param double $price
	 * @param string $content
	 * @param array $more_info
	 * @return array
	 */
	public function submit_quotes($request_id, $user_id, $price, $content, $more_info,$auto)
	{
		$result = array('result'=>0, 'message'=>'', 'quotes_id'=>0);

		$request_id = intval($request_id);
		$user_id = intval($user_id);
		$price = number_format($price*1, 2, '.', '')*1;
		$content = trim($content);
		if( !is_array($more_info) ) $more_info = array();

		if( $request_id<1 || $user_id<1 || $price<=0 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		//����û���profile,����Ҫ�޸�����߼�
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$seller_info = $task_seller_obj->get_seller_info($user_id);
		if( empty($seller_info) )
		{
			$result['result'] = -1;
			$result['message'] = '�̼�Ϊ��';
			return $result;
		}
		$seller_service_id = intval($seller_info['service_id']);

		//����Ƿ񱨹���
		$quotes_id = 0;
		$quotes_info_tmp = $this->get_quotes_info_by_user_id($request_id, $user_id);
		if( !empty($quotes_info_tmp) )
		{
			if( $quotes_info_tmp['is_pay_coins']==1 )
			{
				$result['result'] = -2;
				$result['message'] = '�ظ�����';
				return $result;
			}
			$quotes_id = intval($quotes_info_tmp['quotes_id']);
		}

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_detail_info_by_id($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -3;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		if( !in_array($request_info['status_code'], array('started', 'introduced')) )
		{
			$result['result'] = -3;
			$result['message'] = '�����ѹ���';
			return $result;
		}
		$service_id = intval($request_info['service_id']);

		//��ȡ������Ϣ
		$task_service_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_service_obj->get_service_info($service_id);
		if( empty($service_info) )
		{
			$result['result'] = -4;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$pay_coins = $service_info['pay_coins']*1;
		$pay_rate = $service_info['pay_rate']*1;
		$pay_amount = number_format($price*$pay_rate, 2, '.', '');

		//��ȡ�̼�����
		//����û���profile,����Ҫ�޸�����߼�
		$task_profile_obj = POCO::singleton('pai_task_profile_class');
		$profile_info = $task_profile_obj->get_profile_info($user_id, $seller_service_id);//$service_id
		if( empty($profile_info) )
		{
			$result['result'] = -5;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$profile_id = intval($profile_info['profile_id']);
		$is_vip = intval($profile_info['is_vip']);
		$valid_max = $is_vip?$this->vip_quotes_num:$this->general_quotes_num;

		//�ж��ѱ��۵�����
		$valid_count = $this->get_quotes_list_for_valid($request_id, true);
		if( $valid_count>=$valid_max )
		{
			$result['result'] = -6;
			$result['message'] = '�Ѿ�����';
			return $result;
		}

		$cur_time = time();

		//�������
		$goods_id = (int)$more_info['goods_id'];
		$data = array(
			'request_id' => $request_id,
			'service_id' => $service_id,
			'user_id' => $user_id,
			'goods_id' => $goods_id,
			'is_vip' => $is_vip,
			'profile_id' => $profile_id,
			'price' => $price,
			'content' => $content,
			'total_amount' => $price,
			'pay_amount' => $pay_amount,
			'pay_coins' => $pay_coins,
			'status' => self::STATUS_WAIT,
		);
		if($auto)//�Զ��ɵ�����
		{
			$data['is_pay_coins'] = 1;
			$data['pay_coins_time'] = time();
			$data['pay_coins'] = 0;
			$data['is_read'] = 1;
			$data['read_time'] = time();
			$data['is_read_profile'] = 1;
			$data['read_profile_time'] = time();
		}
		if( $quotes_id>0 )
		{
			$ret = $this->update_quotes_submit($data, $quotes_id);
		}
		else
		{
			$data['add_time'] = $cur_time;
			$quotes_id = $this->add_quotes($data);
			$ret = $quotes_id>0?true:false;
		}
		if( !$ret )
		{
			$result['result'] = -5;
			$result['message'] = '����ʧ��';
			return $result;
		}

		//���������������״̬
		$more_info = array('quotes_time'=>$cur_time);
		$task_lead_obj = POCO::singleton('pai_task_lead_class');
		$task_lead_obj->update_lead_status_quotes($user_id, $request_id, $more_info);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['quotes_id'] = $quotes_id;
		return $result;
	}

	/**
	 * ֧�����⿨
	 * @param int $quotes_id
	 */
	public function pay_quotes_coins($quotes_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$request_id = intval($quotes_info['request_id']);

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_detail_info_by_id($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -3;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		if( !in_array($request_info['status_code'], array('started', 'introduced')) )
		{
			$result['result'] = -3;
			$result['message'] = '�����ѹ���';
			return $result;
		}

		$user_id = intval($quotes_info['user_id']);
		$pay_coins = $quotes_info['pay_coins']*1;

		//��ȡ�û����⿨��Ϣ
		$task_coin_obj = POCO::singleton('pai_task_coin_class');
		$coin_info = $task_coin_obj->get_coin_info($user_id);
		if( empty($coin_info) || $coin_info['balance']<$pay_coins )
		{
			$result['result'] = -4;
			$result['message'] = '���⿨����';
			return $result;
		}

		//��ȡ�̼�����
		$task_profile_obj = POCO::singleton('pai_task_profile_class');
		$profile_info = $task_profile_obj->get_profile_info($quotes_info['profile_id']);
		if( empty($profile_info) )
		{
			$result['result'] = -5;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$is_vip = intval($profile_info['is_vip']);
		$valid_max = $is_vip?$this->vip_quotes_num:$this->general_quotes_num;

		//�ж��ѱ��۵�����
		$valid_count = $this->get_quotes_list_for_valid($request_id, true);
		if( $valid_count>=$valid_max )
		{
			$result['result'] = -6;
			$result['message'] = '�Ѿ�����';
			return $result;
		}

		$cur_time = time();

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//�۳����⿨
		if( $pay_coins>0 )
		{
			$subject = '����֧��';
			$more_info = array('reason_type'=>'quotes', 'reason_rid'=>$quotes_id, 'add_time'=>$cur_time);
			$margin_ret = $task_coin_obj->margin_balance($user_id, -$pay_coins, $subject, $more_info);
			if( $margin_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -7;
				$result['message'] = '���⿨����';
				return $result;
			}
		}

		$more_info = array('pay_coins_time'=>$cur_time);
		$ret = $this->update_quotes_pay_coins($quotes_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -8;
			$result['message'] = '֧��ʧ��';
			return $result;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//�������������ı�������
		$quotes_num = $this->get_quotes_list_for_valid($request_id, true);
		$task_lead_obj = POCO::singleton('pai_task_lead_class');
		$task_lead_obj->update_lead_quotes_num($request_id, $quotes_num);

		//������������
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_message_obj->submit_message($user_id, $quotes_id, 'quotes', $quotes_info['content']);

		//�¼�����
		$trigger_params = array('quotes_id'=>$quotes_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->quotes_pay_coins_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * �˻����⿨
	 * @param int $quotes_id
	 */
	public function refund_quotes_coins($quotes_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) || $quotes_info['is_pay_coins']==0 )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($quotes_info['request_id']);
		if( empty($request_info) )
		{
			$result['result'] = -3;
			$result['message'] = '����Ϊ��';
			return $result;
		}

		$user_id = intval($quotes_info['user_id']);
		$pay_coins = $quotes_info['pay_coins']*1;
		$cut_time = time();

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//�˻����⿨
		if( $pay_coins>0 )
		{
			$subject = '�����˻�';
			$more_info = array('reason_type'=>'quotes', 'reason_rid'=>$quotes_id, 'add_time'=>$cut_time);
			$task_coin_obj = POCO::singleton('pai_task_coin_class');
			$margin_ret = $task_coin_obj->margin_balance($user_id, $pay_coins, $subject, $more_info);
			if( $margin_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -3;
				$result['message'] = '�˻�ʧ��';
				return $result;
			}
		}

		$more_info = array('refund_coins_time'=>$cut_time);
		$ret = $this->update_quotes_refund_coins($quotes_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '�˻�ʧ��';
			return $result;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//�������� refund_coins
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_message_obj->submit_message($request_info['user_id'], $quotes_id, 'refund_coins');

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * ȡ������
	 * @param int $quotes_id
	 * @return array
	 */
	public function cancel_quotes($quotes_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}

		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($quotes_info['request_id']);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}

		$cur_time = time();

		//���±���״̬
		$more_info = array('canceled_time'=>$cur_time);
		$ret = $this->update_quotes_status_canceled($quotes_id, $more_info);
		if( !$ret )
		{
			$result['result'] = -2;
			$result['message'] = 'ʧ��';
			return $result;
		}

		//�������� declined
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_message_obj->submit_message($request_info['user_id'], $quotes_id, 'declined');

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * ��Ӷ
	 * @param int $quotes_id
	 * @return array
	 */
	public function hire_quotes($quotes_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$request_id = intval($quotes_info['request_id']);

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			$result['result'] = -3;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$request_status = intval($request_info['status']);
		if( $request_status===pai_task_request_class::STATUS_HIRED)
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹ�Ӷ';
			return $result;
		}
		if( $request_status===pai_task_request_class::STATUS_CANCELED )
		{
			$result['result'] = -4;
			$result['message'] = '������ȡ��';
			return $result;
		}
		if( $request_status!==pai_task_request_class::STATUS_WAIT )
		{
			$result['result'] = -4;
			$result['message'] = '״̬����';
			return $result;
		}

		$cur_time = time();

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//���±���
		$more_info = array('hired_time'=>$cur_time);
		$ret = $this->update_quotes_status_hired($quotes_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -5;
			$result['message'] = 'ʧ��';
			return $result;
		}

		//��������
		$more_info = array('hired_time'=>$cur_time);
		$ret = $task_request_obj->update_request_status_hired($request_id, $more_info);
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

		//�������� hired declined
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_message_obj->submit_message($request_info['user_id'], $quotes_id, 'hired');
		$quotes_list = $this->get_quotes_list_for_valid($request_id);
		foreach($quotes_list as $val)
		{
			if( $val['status']==0 )
			{
				$task_message_obj->submit_message($request_info['user_id'], $val['quotes_id'], 'declined');
			}
		}

		//�¼�����
		$trigger_params = array('quotes_id'=>$quotes_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->quotes_hire_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * �ύ֧��
	 * @param int $quotes_id
	 * @param double $available_balance ҳ�浱ǰ���
	 * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
	 * @param string $third_code ֧����ʽ alipay_purse tenpay_wxapp�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $notify_url
	 * @return array
	 */
	public function submit_pay_quotes($quotes_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url='')
	{
		$result = array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'');

		$quotes_id = intval($quotes_id);
		$available_balance = number_format($available_balance*1, 2, '.', '')*1;
		$is_available_balance = intval($is_available_balance);
		$third_code = trim($third_code);
		$redirect_url = trim($redirect_url);
		$notify_url = trim($notify_url);

		//������
		if( $quotes_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��������';
			return $result;
		}
		$pay_amount = $quotes_info['pay_amount']*1;
		$is_pay = intval($quotes_info['is_pay']);
		$status = intval($quotes_info['status']);
		if( $is_pay==1 )
		{
			$result['result'] = -3;
			$result['message'] = '��֧��';
			return $result;
		}
		if( $status!==self::STATUS_HIRED)
		{
			$result['result'] = -3;
			$result['message'] = 'δ��Ӷ';
			return $result;
		}

		$payment_info = array(
			'third_code' => $third_code, //������֧����ʽ
			'subject' => 'ԼԼ-�����֧��', //��Ʒ����
			'body' => '', //��Ʒ����
			'amount' => $pay_amount, //֧�����
			'channel_return' => $redirect_url,
			'channel_notify' => $notify_url,
			'channel_merchant' => '',
		);
		$pai_payment_obj = POCO::singleton('pai_payment_class');
		$payment_ret = $pai_payment_obj->submit_payment('quotes', $quotes_id, $payment_info);
		if( $payment_ret['error']!==0 )
		{
			$result['result'] = -4;
			$result['message'] = $payment_ret['message'];
			return $result;
		}

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['payment_no'] = $payment_ret['payment_no'];
		$result['request_data'] = $payment_ret['request_data'];
		return $result;
	}

	/**
	 * �ύ֧��
	 * @param int $quotes_id
	 * @param double $available_balance ҳ�浱ǰ���
	 * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
	 * @param string $third_code ֧����ʽ alipay_purse tenpay_wxapp�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $notify_url
	 * @return array array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'')
	 * result 1��ȡ������֧����2���֧���ɹ�
	 */
	public function submit_pay_quotes_v2($quotes_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url='', $coupon_sn='')
	{
		$result = array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'');

		$quotes_id = intval($quotes_id);
		$available_balance = number_format($available_balance*1, 2, '.', '')*1;
		$is_available_balance = intval($is_available_balance);
		$third_code = trim($third_code);
		$redirect_url = trim($redirect_url);
		$notify_url = trim($notify_url);
		$coupon_sn = trim($coupon_sn);
		if( $quotes_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��������';
			return $result;
		}
		$request_id = intval($quotes_info['request_id']);
		$service_id = intval($quotes_info['service_id']);
		$pay_amount = $quotes_info['pay_amount']*1;
		$total_price = $pay_amount;

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -3;
			$result['message'] = '��������';
			return $result;
		}
		$buyer_user_id = intval($request_info['user_id']);
		$title = trim($request_info['title']);

		$payment_obj = POCO::singleton('pai_payment_class');
		$account_info = $payment_obj->get_user_account_info($buyer_user_id);

		//�Ż�ȯ����
		$channel_module = "task_request";
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$coupon_obj->not_use_coupon_by_oid($channel_module, $quotes_id);

		//�ȸ���Ϊ0
		$data = array(
			'discount_price' => 0,
			'is_use_coupon' => 0,
			'use_coupon_time' => 0,
		);
		$this->update_quotes($data, $quotes_id);

		$discount_price = 0;
		$is_use_coupon = 0;
		if(strlen($coupon_sn)>0)
		{
			$param_info = array(
				'module_type' => $channel_module, //ģ������ waipai yuepai topic
				'order_total_amount' => $pay_amount, //�����ܽ��
				'service_id' => $service_id, //��������ID
			);
			$coupon_ret = $coupon_obj->use_coupon($buyer_user_id, 1, $coupon_sn, $channel_module, $quotes_id, $param_info);
			if( $coupon_ret['result']!=1 )
			{
				$result['result'] = -4;
				$result['message'] = $coupon_ret['message'];
				return $result;
			}
			$discount_price = $coupon_ret['used_amount'];
			$is_use_coupon = 1;

			$total_price = $total_price - $discount_price;
			if( $total_price<0 )
			{
				$coupon_obj->not_use_coupon_by_oid($channel_module, $quotes_id);

				$result['result'] = -5;
				$result['message'] = '�Żݽ������';
				return $result;	//�Ż�ȯ�����ڶ�������ʹ�ɹ�ʹ��Ҳ�˻ء�
			}

			$data = array(
				'discount_price' => $discount_price,
				'is_use_coupon' => $is_use_coupon,
				'use_coupon_time' => time(),
			);
			$this->update_quotes($data, $quotes_id);
		}

		//�����֧��
		if( $is_available_balance )
		{
			if( $account_info['available_balance']<$total_price )
			{
				//���㣬�ñ꣬����ʹ�õ�����
				$amount = $total_price - $account_info['available_balance'];
				$redirect_third = 1;
			}
			else
			{
				//����ʼ
				POCO_TRAN::begin($this->getServerId());

				//�����û������֧�������±��۵�״̬
				$more_info = array(
					'org_user_id' => 0,
					'is_balance' => 1,
					'is_third' => 0,
					'recharge_id' => 0,
					'subject' => $title,
					'remark' => '',
				);
				$submit_ret = $payment_obj->submit_trade_out_v2($channel_module, $request_id, $quotes_id, $buyer_user_id, $pay_amount, $discount_price, $more_info);
				if( $submit_ret['error']!==0 )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -6;
					$result['message'] = $submit_ret['message'];
					return $result;
				}

				$pay_ret = $this->pay_quotes($quotes_info);
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

				$result['result'] = 2;
				$result['message'] = '���֧���ɹ�';
				return $result;
			}
		}
		else
		{
			//ֱ����֧����֧��
			$amount = $total_price;
			$redirect_third = 1;
		}

		if( $redirect_third )
		{
			$openid = '';
			if( $third_code=='tenpay_wxpub' )
			{
				$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
				$bind_info = $bind_weixin_obj->get_bind_info_by_user_id( $buyer_user_id );
				$openid = $bind_info ['open_id'];
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
			$recharge_ret = $payment_obj->submit_recharge($channel_module, $buyer_user_id, $amount, $third_code, $request_id, $quotes_id, 0, $more_info);
			if( $recharge_ret['error']!==0 )
			{
				$result['result'] = -9;
				$result['message'] = '��ת��������֧����������  ��ϸ��Ϣ��recharge_ret';
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
	 * @param array $quotes_info
	 * @return bool
	 */
	public function pay_quotes($quotes_info)
	{
		if( !is_array($quotes_info) || empty($quotes_info) )
		{
			return false;
		}
		$quotes_id = intval($quotes_info['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}

		$cur_time = time();

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//���±���֧��״̬
		$more_info = array('pay_time'=>$cur_time);
		$ret = $this->update_quotes_pay($quotes_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//��������֧��״̬
		$more_info = array('pay_time'=>$cur_time);
		$ret = $task_request_obj->update_request_pay($request_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//�������� earnest
		$more_info = array('pay_amount'=>$quotes_info['pay_amount']);
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_message_obj->submit_message($request_info['user_id'], $quotes_id, 'earnest', '', $more_info);

		//�¼�����
		$trigger_params = array('quotes_id'=>$quotes_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->quotes_pay_after($trigger_params);

		return true;
	}

	/**
	 * ֧�����ۣ�����֧����Ϣ
	 * @param array $payment_info
	 * @return array
	 */
	public function pay_quotes_by_payment_info($payment_info)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		if( !is_array($payment_info) || empty($payment_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		$channel_module = trim($payment_info['channel_module']);
		$payment_status = intval($payment_info['status']);
		if( $channel_module!='quotes' || $payment_status!=8 )
		{
			$result['result'] = -2;
			$result['message'] = '֧������';
			return $result;
		}
		$payment_no = trim($payment_info['payment_no']);
		$quotes_id = intval($payment_info['channel_rid']); //����ID
		$third_total_fee = $payment_info['third_total_fee']*1; //ʵ�ս��

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -4;
			$result['message'] = '����Ϊ��';
			return $result;
		}

		//��֧��
		if( $quotes_info['is_pay']==1 )
		{
			if( $payment_no==$quotes_info['payment_no'] )
			{
				$result['result'] = 1;
				$result['message'] = '�ɹ�';
				return $result;
			}
			else
			{
				$result['result'] = -5;
				$result['message'] = '�ظ�֧��';
				return $result;
			}
		}
		if( $quotes_info['is_pay']!=0 )
		{
			$result['result'] = -6;
			$result['message'] = '״̬����';
			return $result;
		}

		//�����
		if($third_total_fee<=0 || $quotes_info['pay_amount']!=$third_total_fee)
		{
			$result['result'] = -7;
			$result['message'] = '������';
			return $result;
		}

		//����
		$ret = $this->pay_quotes($quotes_info);
		if( !$ret )
		{
			$result['result'] = -8;
			$result['message'] = '���۴���';
			return $result;
		}

		$this->update_quotes(array('payment_no'=>$payment_no), $quotes_id);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * ֧�����ۣ�����֧����Ϣ
	 * @param array $payment_info
	 * @return array result 1,֧���ɹ���else ֧��ʧ��
	 */
	public function pay_quotes_by_payment_info_v2($payment_info)
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
		$quotes_id = intval($channel_param_arr['enroll_id_str']);

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -6;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$request_id = intval($quotes_info['request_id']);
		$pay_amount = $quotes_info['pay_amount']*1;
		$is_use_coupon = intval($quotes_info['is_use_coupon']);
		$discount_price = $quotes_info['discount_price']*1;
		$pending_amount = $pay_amount - $discount_price;
		
		//��֧��
		if( $quotes_info['is_pay']==1 )
		{
			if( $payment_no==$quotes_info['payment_no'] )
			{
				$result['result'] = 1;
				$result['message'] = '�ɹ�';
				return $result;
			}

			$result['result'] = -7;
			$result['message'] = '�ظ�֧��';
			return $result;
		}

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -8;
			$result['message'] = '��������';
			return $result;
		}
		$buyer_user_id = intval($request_info['user_id']);
		$title = trim($request_info['title']);
		
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
			'org_user_id' => 0,
			'is_balance' => $is_balance,
			'is_third' => 1,
			'recharge_id' => $payment_info['channel_rid'],
			'subject' => $title,
			'remark' => '',
		);
		$payment_obj = POCO::singleton('pai_payment_class');
		$submit_ret = $payment_obj->submit_trade_out_v2('task_request', $request_id, $quotes_id, $buyer_user_id, $pay_amount, $discount_price, $more_info);
		if( $submit_ret['error']!==0 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -9;
			$result['message'] = $submit_ret['message'];
			return $result;
		}

		$pay_ret = $this->pay_quotes($quotes_info);
		if( !$pay_ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -10;
			$result['message'] = '֧��ʧ��';
			return $result;
		}
		
		$this->update_quotes(array('payment_no'=>$payment_no), $quotes_id);
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '֧���ɹ�';
		return $result;
	}

	/**
	 * �����Ѳ鿴����
	 * @param int $quotes_id
	 * @return bool
	 */
	public function read_quotes($quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) || $quotes_info['is_read']!=0 )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}

		//�����Ѳ鿴״̬
		$more_info = array('read_time'=>time());
		$ret = $this->update_quotes_read($quotes_id, $more_info);
		if( !$ret )
		{
			return false;
		}

		//�������� read_quotes
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_message_obj->submit_message($request_info['user_id'], $quotes_id, 'read_quotes');

		//�¼�����
		$trigger_params = array('quotes_id'=>$quotes_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->quotes_read_after($trigger_params);

		return true;
	}

	/**
	 * �����Ѳ鿴��������
	 * @param int $quotes_id
	 * @return bool
	 */
	public function read_profile($quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}

		//�����Ѳ鿴״̬
		$more_info = array('read_profile_time'=>time());
		$ret = $this->update_quotes_read_profile($quotes_id, $more_info);
		if( $ret )
		{
			$task_message_obj = POCO::singleton('pai_task_message_class');
			$task_message_obj->submit_message($request_info['user_id'], $quotes_id, 'read_profile');
		}

		return $ret;
	}

	/**
	 * ������ϸ��Ϣ
	 * @param array $list
	 * @return array
	 */
	private function fill_quotes_detail_list($list)
	{
		if( !is_array($list) )
		{
			return $list;
		}

		$task_request_obj = POCO::singleton('pai_task_request_class');

		$cur_time = time();
		foreach($list as $key=>$info)
		{
			$quotes_id = intval($info['quotes_id']);
			$user_id = intval($info['user_id']);
			$user_nickname = get_user_nickname_by_user_id($user_id);
			$user_icon = get_user_icon($user_id);

			$request_info = $task_request_obj->get_request_info($info['request_id']);

			$info['user_nickname'] = $user_nickname;
			$info['user_icon'] = $user_icon;
			$info['buyer_user_icon'] = get_user_icon($request_info['user_id']);
			$info['buyer_user_id'] = $request_info['user_id'];
			$info['buyer_nickname'] = get_user_nickname_by_user_id($request_info['user_id']);
			$list[$key] = $info;
		}

		return $list;
	}

	/**
	 * ���������۱���
	 * @param int $quotes_id
	 * @return bool
	 */
	public function review_quotes($quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return false;
		}

		//��ȡ������Ϣ
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);

		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);

		$cur_time = time();

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//���±�������״̬
		$more_info = array('review_time'=>$cur_time);
		$ret = $this->update_quotes_review($quotes_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//������������״̬
		$more_info = array('review_time'=>$cur_time);
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$ret = $task_request_obj->update_request_review($request_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//�������� review
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_message_obj->submit_message($buyer_user_id, $quotes_id, 'review');

		//�¼�����
		$trigger_params = array('quotes_id'=>$quotes_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->quotes_review_after($trigger_params);

		return true;
	}

	/**
	 * ��ȡ������Ϣ
	 * @param int $quotes_id
	 * @return array
	 */
	public function get_quotes_detail_info_by_id($quotes_id)
	{
		$quotes_info = $this->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return array();
		}
		$quotes_detail_list = $this->fill_quotes_detail_list(array($quotes_info));
		$quotes_detail_info = $quotes_detail_list[0];
		if( !is_array($quotes_detail_info) ) $quotes_detail_info = array();
		return $quotes_detail_info;
	}

	/**
	 * ��ȡ��Ч�ı����б���������ID
	 * @param int $request_id
	 * @return array
	 */
	public function get_quotes_detail_list_for_valid($request_id)
	{
		$quotes_list = $this->get_quotes_list_for_valid($request_id, false);
		return $this->fill_quotes_detail_list($quotes_list);
	}

	/**
	 * ��ȡĳ�������������������û�ID
	 * @param int $user_id
	 * @param int $quotes_id
	 * @return int
	 */
	public function get_quotes_remind_num($user_id, $quotes_id)
	{
		$task_message_obj = POCO::singleton('pai_task_message_class');
		return $task_message_obj->get_message_count_unread($user_id, $quotes_id);
	}

	/**
	 * ��ȡ�Ƿ��ɫ
	 * @param int $quotes_status
	 * @param string $request_status_code
	 * @return string
	 */
	public function get_quotes_is_gray($quotes_status, $request_status_code)
	{
		$result = '0';
		$quotes_status = intval($quotes_status);
		$request_status_code = trim($request_status_code);
		if( $request_status_code=='started' ) //����Ӷ�������ڣ��ޱ���
		{
			$result = '0';
		}
		elseif( $request_status_code=='introduced' ) //����Ӷ�������ڣ��б���
		{
			$result = '0';
		}
		elseif( $request_status_code=='closed' ) //����Ӷ���ѹ��ڣ��ޱ���
		{
			$result = '1';
		}
		elseif( $request_status_code=='quoted' ) //����Ӷ���ѹ��ڣ��б���
		{
			$result = '0';
		}
		elseif( $request_status_code=='canceled' ) //��ȡ��
		{
			$result = '1';
		}
		elseif( $request_status_code=='hired' ) //�ѹ�Ӷ����֧��
		{
			$result = ($quotes_status===self::STATUS_HIRED)?'0':'1';
		}
		elseif( $request_status_code=='paid' ) //�ѹ�Ӷ����֧����������
		{
			$result = ($quotes_status===self::STATUS_HIRED)?'0':'1';
		}
		elseif( $request_status_code=='reviewed' ) //�ѹ�Ӷ����֧����������
		{
			$result = ($quotes_status===self::STATUS_HIRED)?'0':'1';
		}
		return $result;
	}

	/*
	 * ��ȡ���������б�
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function get_pending_quotes_list($user_id, $b_select_count=false, $limit='0,20',$where_str='', $order_by='quotes_id desc')
	{
		$user_id = (int)$user_id;

		//�����ѯ����
		$sql_where = "user_id={$user_id} and status!=1 and is_archive=0";
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		$ret = $this->get_quotes_list(0, $user_id, $b_select_count, $sql_where, $order_by, $limit, '*');

		if($b_select_count==true)
		{
			return $ret;
		}

		$ret = $this->fill_quotes_pc_detail_list($ret);


		return $ret;
	}


	/*
	 * ��ȡ�����ж����б�
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function get_process_quotes_list($user_id, $b_select_count=false, $limit='0,20',$where_str='', $order_by='quotes_id desc')
	{
		$user_id = (int)$user_id;
		$sql_where = "user_id={$user_id} and status=1";
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		$ret = $this->get_quotes_list(0, $user_id, $b_select_count, $sql_where, $order_by, $limit, '*');

		if($b_select_count==true)
		{
			return $ret;
		}
		else
		{
			return $this->fill_quotes_pc_detail_list($ret);
		}
	}

	/*
	 * ��ȡ�ѹ鵵�����б�
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function get_archive_quotes_list($user_id, $b_select_count=false, $limit='0,20',$where_str='', $order_by='quotes_id desc')
	{
		$user_id = (int)$user_id;
		$sql_where = "user_id={$user_id} and is_archive=1";
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		$ret = $this->get_quotes_list(0, $user_id, $b_select_count, $sql_where, $order_by, $limit, '*');

		if($b_select_count==true)
		{
			return $ret;
		}
		else
		{
			return $this->fill_quotes_pc_detail_list($ret);
		}
	}

	/**
	 * ��ȡ��Ϣ���Ա�����
	 * @return array
	 */
	public function get_user_id_and_count_list_for_remind()
	{
		$where_str = "is_important=1";
		$this->set_task_quotes_tbl();
		$sql = "SELECT user_id,COUNT(user_id) AS count FROM {$this->_db_name}.{$this->_tbl_name} WHERE {$where_str} GROUP BY user_id ORDER BY user_id ASC";
		return $this->findBySql($sql);
	}

	/**
	 * ����PC��ϸ��Ϣ
	 * @param array $list
	 * @return array
	 */
	private function fill_quotes_pc_detail_list($list)
	{
		if( !is_array($list) )
		{
			return $list;
		}

		$task_request_obj = POCO::singleton('pai_task_request_class');
		$task_lead_obj = POCO::singleton('pai_task_lead_class');

		$cur_time = time();
		foreach ($list as $k=>$val)
		{
			$request_info = $task_request_obj->get_request_info($val['request_id']);

			//����״̬ͼ��
			$order_status = '';

			if( $val['status']==2 ) //��ȡ��
			{
				$order_status = "closed"; //���ܾ�
			}
			else
			{
				if( $val['is_pay_coins']==0 ) //���⿨δ֧��
				{
					if( $cur_time<$request_info['expire_time'] ) //���⿨δ֧��������δ����
					{
						if( $request_info['status']==0 ) //���⿨δ֧��������δ���ڣ�����δ��Ӷ
						{
							$order_status = "pending"; //������
						}
						else  //���⿨δ֧��������δ���ڣ������ѹ�Ӷ������ȡ����
						{
							$order_status = "closed"; //���ܾ�
						}
					}
					else //���⿨δ֧���������ѹ���
					{
						$order_status = "expired"; //������
					}
				}
				else //���⿨��֧��
				{
					if( $val['is_refund_coins']==0 ) //���⿨��֧�������⿨δ�˻�
					{
						if( $val['is_read']==0 ) //���⿨��֧�������⿨δ�˻���δ���鿴
						{
							$order_status = "sent"; //�ѷ���
						}
						else //���⿨��֧�������⿨δ�˻����ѱ��鿴
						{
							$order_status = "viewed"; //�Ѳ鿴
						}
					}
					else //���⿨��֧�������⿨���˻�
					{
						$order_status = "refunded"; //���˻�
					}
				}

			}

			$list[$k]['order_status'] = $order_status;

			$list[$k]['time_format'] = yue_time_format_convert($val['add_time']);
			$list[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
			$list[$k]['title'] = $request_info['title'];
			$list[$k]['when_str'] = $request_info['when_str'];
			$list[$k]['where_str'] = $request_info['where_str'];
			$list[$k]['buyer_nickname'] = get_user_nickname_by_user_id($request_info['user_id']);

			if($val['is_pay_coins']==0)
			{
				$lead_list = $task_lead_obj->get_lead_list_by_user_id($val['user_id'],false, 'request_id='.$val['request_id']);
				$list[$k]['lead_id'] = $lead_list[0]['lead_id'];
			}
		}

		return $list;
	}

	/**
	 * ��ʱ�˻����⿨
	 * @return array array('success_num'=>0, 'fail_num'=>0)
	 */
	public function refund_quotes_coins_by_timing()
	{
		$result = array('success_num'=>0, 'fail_num'=>0);

		$success_num = 0;
		$fail_num = 0;
		$end_time = time() - $this->refund_coins_seconds;
		$quotes_list = $this->get_quotes_list(0, 0, false, "is_pay_coins=1 AND pay_coins_time<={$end_time} AND is_refund_coins=0 AND is_read=0 AND status<>1", 'pay_coins_time ASC', '0,1000');
		foreach($quotes_list as $quotes_info)
		{
			$refund_ret = $this->refund_quotes_coins($quotes_info['quotes_id']);
			if( $refund_ret['result']==1 )
			{
				$success_num++;
			}
			else
			{
				$fail_num++;
			}
		}

		$result['success_num'] = $success_num;
		$result['fail_num'] = $fail_num;
		return $result;
	}


	/**
	 * ��ȡ�û��������֧������ķ��� //�����4��
	 * @param int $user_id //�û�id
	 * @return array
	 */
	public function get_quotes_history_by_userid($user_id,$limit = 4)
	{
		$user_id = (int)$user_id;
		$limit = (int)$limit;
		if(!$user_id)
		{
			return array();
		}
		$limit = $limit?$limit:4;
		//$re = $this->findAll($where, $limit, $sort);
		$sql = 'select r.request_id,r.title,q.pay_time from '.$this->_db_name.'.task_quotes_tbl as q,'.$this->_db_name.'.task_request_tbl as r where q.user_id = "'.$user_id.'" AND q.is_pay=1 and q.request_id=r.request_id order by q.pay_time desc limit 0,'.$limit;
		$re = $this->findBySql($sql);
		return $re;
	}


	/**
	 * ��ȡ�û��������֧������ķ��� //�����4��
	 * @param int $profile_id //�û�profile_id
	 * @return array
	 */
	public function get_quotes_history_by_profile($profile_id,$limit = 4)
	{
		$profile_id = (int)$profile_id;
		$limit = (int)$limit;
		if(!$user_id)
		{
			return array();
		}
		$limit = $limit?$limit:4;
		$sql = 'select r.request_id,r.title,q.pay_time from '.$this->_db_name.'.task_quotes_tbl as q,'.$this->_db_name.'.task_request_tbl as r where q.profile_id = "'.$profile_id.'" AND q.is_pay=1 and q.request_id=r.request_id order by q.pay_time desc limit 0,'.$limit;
		$re = $this->findBySql($sql);
		return $re;
	}
}
