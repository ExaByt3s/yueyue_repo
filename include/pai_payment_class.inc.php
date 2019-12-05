<?php
/**
 * ֧����
 * @author Henry
 */

class pai_payment_class extends POCO_TDG
{
	/**
	 * ������ʶ
	 * @var string
	 */
	private $channel_code = 'pai';

	/**
	 * ����ģ��
	 * @var string
	 */
	private $channel_module = 'recharge';
	
	/**
	 * �û��˻�����
	 * @var string
	 */
	private $user_account_type = 'actual';
	
	/**
	 * ���ý��˻�����
	 * @var string
	 */
	private $bail_account_type = 'bail';

	/**
	 * ��С��֧�����
	 * @var int
	 */
	private $min_pay_amount = 10;
	
	/**
	 * ����֧�����
	 * @var string
	 */
	private $max_pay_amount = 10000;

	/**
	 * ����˻�����
	 * @var string
	 */
	private $event_account_type = 'virtual';
	
	/**
	 * ���ѽ���˻�����
	 * @var string
	 */
	private $consume_account_type = 'consume';
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		//���︨������ʵʱ���ݡ�����Ŀǰֻ�е�¼�Ż�ʵʱ��û��¼״̬���첽֪ͨ����ʱִ�п��ܻ�����⡣
		define('G_DB_GET_REALTIME_DATA', 1);
		
		if( defined('G_PAI_ECPAY_DEV') ){
			//����ģʽ
			$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dev_dir');
			include_once $ecpay_app_dir . '/poco_app_common.inc.php';

		}
		else{
			
			$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dir');
			include_once $ecpay_app_dir . '/poco_app_common.inc.php';
		
		}
	}
	
	/**
	 * ��ȡ����ģ��
	 * @param string $type_icon
	 * @return string
	 */
	public function get_channel_module_by_type_icon($type_icon)
	{
		$type_icon = trim($type_icon);
		return ($type_icon=='yuepai_app' ? 'yuepai' : 'waipai');
	}
	
	/**
	 * ��ȡ����ģ��
	 * @param int $event_id
	 * @return string
	 */
	public function get_channel_module_by_event_id($event_id)
	{
		$event_id = intval($event_id);
		if( $event_id<1 )
		{
			return '';
		}
		$details_obj = POCO::singleton('event_details_class');
		$event_info  = $details_obj->get_event_by_event_id($event_id);
		return $this->get_channel_module_by_type_icon($event_info['type_icon']);
	}
	
	/**
	 * ��ȡ����ģ��
	 * @param int $enroll_id
	 * @return string
	 */
	public function get_channel_module_by_enroll_id($enroll_id)
	{
		$enroll_id = intval($enroll_id);
		if( $enroll_id<1 )
		{
			return '';
		}
		$enroll_obj = POCO::singleton('event_enroll_class');
		$enroll_info = $enroll_obj->get_enroll_by_enroll_id($enroll_id);
		return $this->get_channel_module_by_event_id($enroll_info['event_id']);
	}
	
	/**
	 * ��ȡǮ���˻�
	 * @param int $user_id
	 * @return array
	 */
	public function get_purse_account_info($user_id)
	{
		$account_obj = POCO::singleton('ecpay_account_v2_class', array($this->channel_code));
		return $account_obj->get_account_info_by_channel($this->user_account_type, $user_id); //Ǯ���˻�
	}
	
	/**
	 * ��ȡǮ���������֣�
	 * @param int $user_id
	 * @return double
	 */
	public function get_purse_available_balance($user_id)
	{
		$account_info = $this->get_purse_account_info($user_id);
		if( empty($account_info) )
		{
			return 0;
		}
		return $account_info['available_balance'];
	}
	
	/**
	 * ��ȡ���ѽ��˻�
	 * @param int $user_id
	 * @return array
	 */
	public function get_consume_account_info($user_id)
	{
		$account_obj = POCO::singleton('ecpay_account_v2_class', array($this->channel_code));
		return $account_obj->get_account_info_by_channel($this->consume_account_type, $user_id); //Ǯ���˻�
	}
	
	/**
	 * ��ȡ���ѽ����������֣�
	 * @param int $user_id
	 * @return double
	 */
	public function get_consume_available_balance($user_id)
	{
		$account_info = $this->get_consume_account_info($user_id);
		if( empty($account_info) )
		{
			return 0;
		}
		return $account_info['available_balance'];
	}
	
	/**
	 * ��ȡ�û��˻�
	 * @param int $user_id
	 * @return array
	 */
	public function get_user_account_info($user_id)
	{
		$user_account_info = $this->get_purse_account_info($user_id); //Ǯ���˻�
		if( empty($user_account_info) )
		{
			return array();
		}
		
		//���ݾɴ��룬�ϼƼ������ѽ�
		$consume_account_info = $this->get_consume_account_info($user_id); //���ѽ��˻�
		$user_account_info['balance'] = bcadd($user_account_info['balance'], $consume_account_info['balance'], 2);
		$user_account_info['receivable'] = bcadd($user_account_info['receivable'], $consume_account_info['receivable'], 2);
		$user_account_info['payable'] = bcadd($user_account_info['payable'], $consume_account_info['payable'], 2);
		$user_account_info['available_balance'] = bcadd($user_account_info['available_balance'], $consume_account_info['available_balance'], 2);
		
		return $user_account_info;
	}
	
	/**
	 * ��ȡ�û��������
	 * @param int $user_id
	 * @return double
	 */
	public function get_user_available_balance($user_id)
	{
		$account_info = $this->get_user_account_info($user_id);
		if( empty($account_info) )
		{
			return 0;
		}
		return $account_info['available_balance'];
	}
	
	/**
	 * ��ȡ��˻�
	 * @param int $event_id
	 * @return array
	 */
	public function get_event_account_info($event_id)
	{
		$event_id = intval($event_id);
		$channel_module = $this->get_channel_module_by_event_id($event_id);
		if( $event_id<1 || strlen($channel_module)<1 )
		{
			return array();
		}
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_event_account_info($channel_module, $event_id);
	}
	
	/**
	 * ��ȡ���ý��˻�
	 * @param int $user_id
	 * @return array
	 */
	public function get_bail_account_info($user_id)
	{
		$account_obj = POCO::singleton('ecpay_account_v2_class', array($this->channel_code));
		return $account_obj->get_account_info_by_channel($this->bail_account_type, $user_id);
	}
	
	/**
	 * ��ȡ���ý�������
	 * @param int $user_id
	 * @return double
	 */
	public function get_bail_available_balance($user_id)
	{
		$account_info = $this->get_bail_account_info($user_id);
		if( empty($account_info) )
		{
			return 0;
		}
		return $account_info['available_balance'];
	}
	
	/**
	 * ԼԼ�Ż�ȯ�˻����û�ID
	 * @return int
	 */
	public function get_coupon_user_id()
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_coupon_user_id();
	}
	
	/**
	 * ��ȡ�˺�ͳ��
	 * @param string $account_type
	 * @param int $account_rid
	 * @param int $begin_time
	 * @param int $end_time
	 * @return array
	 */
	public function get_stat_account_list($account_type, $account_rid, $begin_time, $end_time)
	{
		$account_obj = POCO::singleton('ecpay_account_v2_class', array($this->channel_code));
		return $account_obj->get_stat_account_list($account_type, $account_rid, $begin_time, $end_time);
	}
	
	/**
	 * ��ȡ֧����Ϣ
	 * @param string $payment_no
	 * @return array
	 */
	public function get_payment_info($payment_no)
	{
		$payment_obj = POCO::singleton('ecpay_payment_class');
		return $payment_obj->get_payment_info($payment_no);
	}
	
	/**
	 * ��ȡ��ֵ��Ϣ
	 * @param int $recharge_id
	 * @return array
	 */
	public function get_recharge_info($recharge_id)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_recharge_info($recharge_id);
	}
	
	/**
	 * ��ȡ��ֵ��Ϣ�����ݱ���ID
	 * ��ûʹ�õ�����֧�������ȡ����
	 * @param int $enroll_id
	 * @return array
	 */
	public function get_recharge_info_by_enroll_id($enroll_id)
	{
		$trade_info = $this->get_trade_info_by_enroll_id($enroll_id);
		if( empty($trade_info) )
		{
			return array();
		}
		
		$is_third = intval($trade_info['is_third']);
		if( $is_third!=1 )
		{
			return array();
		}
		
		$recharge_id = intval($trade_info['recharge_id']);
		return $this->get_recharge_info($recharge_id);
	}
	
	/**
	 * �ύ֧��
	 * @param string $channel_module ����ģ�飬��ֵrecharge
	 * @param string $channel_rid ��������ID����ֵID
	 * @param array $payment_info
	 * @return array
	 * @tutorial
	 * $payment_info = array(
	 * 'third_code' => '',	//������֧����ʽ
	 * 'subject' => '',	//��Ʒ����
	 * 'body' => '',	//��Ʒ����
	 * 'amount' => '',	//֧�����
	 * 'channel_return' => '',
	 * 'channel_notify' => '',
	 * 'channel_merchant' => '',
	 * );
	 */
	public function submit_payment($channel_module, $channel_rid, $payment_info)
	{
		$payment_obj = POCO::singleton('ecpay_payment_class');
		return $payment_obj->submit_payment($this->channel_code, $channel_module, $channel_rid, $payment_info);
	}
	
	/**
	 * �ύ��ֵ��¼��׼��ȥ֧��
	 * @param string $recharge_type ��ֵ���� recharge consume bail activity date activity_pc task_request task_coin mall_order
	 * @param int $user_id
	 * @param double $amount
	 * @param string $third_code
	 * @param int $event_id
	 * @param string $enroll_id_str
	 * @param int $date_id
	 * @param array $more_info
	 * @return array
	 * 
	 * @tutorial
	 * $more_info = array(
	 * 	'channel_return' => '',	//֧���ɹ���ͬ����תҳ��
	 * 	'channel_notify' => '',	//֧���ɹ����첽֪ͨҳ��
	 * );
	 * 
	 */
	public function submit_recharge($recharge_type, $user_id, $amount, $third_code, $event_id=0, $enroll_id_str='', $date_id=0, $more_info=array())
	{
		$result = array();
		
		$recharge_type = trim($recharge_type);
		$user_id = intval($user_id);
		$amount = number_format($amount*1, 2, '.', '')*1;
		$third_code = trim($third_code);
		$event_id = intval($event_id);
		$enroll_id_str = trim($enroll_id_str);
		$date_id = intval($date_id);
		
		if( !in_array($recharge_type, array('recharge', 'consume', 'bail', 'activity', 'date', 'activity_pc', 'task_request', 'task_coin', 'mall_order')) )
		{
			$result['error'] = 11;
			$result['message'] = 'recharge_type����';
			$result['request_data'] = '';
			return $result;
		}
		
		if( $user_id<1 )
		{
			$result['error'] = 12;
			$result['message'] = 'user_id����';
			$result['request_data'] = '';
			return $result;
		}
		
		if( $amount<=0 )
		{
			$result['error'] = 13;
			$result['message'] = 'amount����';
			$result['request_data'] = '';
			return $result;
		}
		
		if( strlen($third_code)<1 )
		{
			$result['error'] = 14;
			$result['message'] = 'third_code����';
			$result['request_data'] = '';
			return $result;
		}
		
		if( in_array($recharge_type, array('activity', 'activity_pc', 'task_request', 'task_coin', 'mall_order')) )
		{
			if( $event_id<1 )
			{
				$result['error'] = 15;
				$result['message'] = 'event_id����';
				$result['request_data'] = '';
				return $result;
			}
			
			if( strlen($enroll_id_str)<1 )
			{
				$result['error'] = 16;
				$result['message'] = 'enroll_id_str����';
				$result['request_data'] = '';
				return $result;
			}
		}
		elseif( in_array($recharge_type, array('date')) )
		{
			if( $date_id<1 )
			{
				$result['error'] = 17;
				$result['message'] = 'date_id����';
				$result['request_data'] = '';
				return $result;
			}
        }
        
        /*
        if( $third_code=='tenpay_wxapp' && !in_array($user_id, array(100003)) )
        {
        	$result['error'] = 18;
        	$result['message'] = '΢��֧�������У�����ʹ��������ʽ��';
        	$result['request_data'] = '';
        	return $result;
        }
        */
		
		$subject = '';
		$channel_return = trim($more_info['channel_return']);
		$channel_notify = trim($more_info['channel_notify']);
		$openid 		= trim($more_info['openid']);
		if( $recharge_type=='recharge' )
		{
			$subject = 'Ǯ����ֵ';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/recharge_notify_url');
		}
		elseif( $recharge_type=='consume' )
		{
			$subject = '���߳�ֵ';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/recharge_notify_url');
		}
		elseif( $recharge_type=='bail' )
		{
			$subject = '���ý��ֵ';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/recharge_notify_url');
		}
		elseif( $recharge_type=='activity' )
		{
			$subject = '�����';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/activity_notify_url');
		}
		elseif( $recharge_type=='date' )
		{
			$subject = 'Լ������';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/date_notify_url');
		}
		elseif( $recharge_type=='activity_pc' )
		{
			$subject = '�����[PC]';
			if( empty($channel_return) ) $channel_return = POCO_APP_PAI::ini('payment/activity_pc_return_url');
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/activity_pc_notify_url');
		}
		elseif( $recharge_type=='task_request' )
		{
			$subject = '�����֧��';
		}
		elseif( $recharge_type=='task_coin' )
		{
			$subject = '���⿨����';
		}
		elseif( $recharge_type=='mall_order' )
		{
			$subject = '����֧��';
		}
		$recharge_info = array(
			'subject'=> $subject,
			'recharge_type' => $recharge_type,
			'user_id' => $user_id,
			'amount' => $amount,
			'third_code' => $third_code,
			'remark' =>'',
			'event_id' => $event_id,
			'enroll_id_str' => $enroll_id_str,
			'date_id' => $date_id
		);
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		$recharge_id = $recharge_obj->submit_recharge( $recharge_info );
		if( $recharge_id<1 )
		{
			$result['error'] = 18;
			$result['message'] = 'recharge_id����';
			$result['request_data'] = '';
			return $result;
		}
		
		$channel_param_arr = array(
			'event_id' => $event_id,
			'enroll_id_str' => $enroll_id_str,
			'date_id' => $date_id,
		);
		$channel_param = serialize($channel_param_arr);
		if( in_array($recharge_type, array('recharge', 'consume', 'bail')) )
		{
			$disable_credit = 1;
		}
		else
		{
			$disable_credit = 0;
		}
		$payment_info = array(
			'channel_param' => $channel_param,
			'disable_credit'=>$disable_credit,
			'third_code' => $third_code,
			'subject' => 'ԼԼ-'.$subject,
			'amount' => $amount,
			'channel_return' => $channel_return,
			'channel_notify' => $channel_notify,
			'openid' 		 => $openid
		);
		
		$payment_obj = POCO::singleton('ecpay_payment_class');
		return $payment_obj->submit_payment($this->channel_code, $this->channel_module, $recharge_id, $payment_info);
	}
	
	/**
	 * �ֶ���ֵ
	 * @param string $recharge_type ��ֵ���� recharge bail activity date activity_pc
	 * @param int $user_id
	 * @param double $amount
	 * @param int $event_id
	 * @param string $enroll_id_str
	 * @param int $date_id
	 * @param array $more_info
	 * @return array
	 * 
	 * @tutorial
	 * $more_info = array(
	 * 	'third_code' => '', //֧����ʽ
	 * 	'third_oid' => '', //��ˮ��
	 *  'real_name' => '',	//�����ʵ����
	 *  'third_buyer' => '',//����˺�
	 *  'third_seller' => '',//�����˺�
	 * 	'ref_id' => 0, //����ID��OA������ID
	 * 	'receive_time' => 0, //�տ�ʱ��
	 * 	'remark' => '', //�տע
	 *  'subject' => '', //��Ʒ����
	 *  'body' => '', //��Ʒ����
	 * );
	 * 
	 */
	public function manual_recharge($recharge_type, $user_id, $amount, $event_id=0, $enroll_id_str='', $date_id=0, $more_info=array())
	{
		$result = array();
		
		$recharge_type = trim($recharge_type);
		$user_id = intval($user_id);
		$amount = number_format($amount*1, 2, '.', '')*1;
		$event_id = intval($event_id);
		$enroll_id_str = trim($enroll_id_str);
		$date_id = intval($date_id);
		$third_code = trim($more_info['third_code']);
		$third_oid = trim($more_info['third_oid']);
		$real_name = trim($more_info['real_name']);
		$third_buyer = trim($more_info['third_buyer']);
		$third_seller = trim($more_info['third_seller']);
		$ref_id = intval($more_info['ref_id']);
		$receive_time = intval($more_info['receive_time']);
		$remark = trim($more_info['remark']);
		$subject = trim($more_info['subject']);
		$body = trim($more_info['body']);
		
		if( !in_array($recharge_type, array('recharge', 'consume', 'bail', 'activity', 'date', 'activity_pc', 'task_request', 'task_coin', 'mall_order')) || $user_id<1 || $amount<=0)
		{
			$result['error'] = 10;
			$result['message'] = '��������';
			return $result;
		}
		
		if( in_array($recharge_type, array('activity', 'activity_pc', 'mall_order')) )
		{
			if( $event_id<1 || strlen($enroll_id_str)<1 )
			{
				$result['error'] = 11;
				$result['message'] = '��������';
				return $result;
			}
		}
		elseif( in_array($recharge_type, array('date')) )
		{
			if( $date_id<1 )
			{
				$result['error'] = 12;
				$result['message'] = '��������';
				return $result;
			}
		}
		
		$subject = '';
		if( $recharge_type=='recharge' )
		{
			$subject = 'Ǯ����ֵ';
		}
		if( $recharge_type=='consume' )
		{
			$subject = '���߳�ֵ';
		}
		elseif( $recharge_type=='bail' )
		{
			$subject = '���ý��ֵ';
		}
		elseif( $recharge_type=='activity' )
		{
			$subject = '�����';
		}
		elseif( $recharge_type=='date' )
		{
			$subject = 'Լ������';
		}
		elseif( $recharge_type=='activity_pc' )
		{
			$subject = '�����[PC]';
		}
		elseif( $recharge_type=='task_request' )
		{
			$subject = '�����֧��';
		}
		elseif( $recharge_type=='task_coin' )
		{
			$subject = '���⿨����';
		}
		elseif( $recharge_type=='mall_order' )
		{
			$subject = '����֧��';
		}
		
		$more_info = array(
			'third_code' => $third_code, //֧����ʽ������
			'third_oid' => $third_oid, //��ˮ�ţ�����
			'real_name' => $real_name,	//�����ʵ����
			'third_buyer' => $third_buyer,//����˺�
			'third_seller' => $third_seller,//�����˺�
			'ref_id' => $ref_id, //����ID��OA������ID
			'receive_time' => $receive_time, //�տ�ʱ��
			'remark' => $remark, //�տע
			'subject' => $subject, //��Ʒ����
			'body' => '', //��Ʒ����
		);
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		$recharge_rst = $recharge_obj->manual_recharge($recharge_type, $user_id, $amount, $event_id, $enroll_id_str, $date_id, $more_info);
		if( $recharge_rst['error']!==0 )
		{
			$result['error'] = 20;
			$result['message'] = $recharge_rst['message'];
			return $result;
		}
		
		$result['error'] = 0;
		$result['message'] = '�ɹ�';
		$result['recharge_id'] = $recharge_rst['recharge_id'];
		$result['payment_no'] = $recharge_rst['payment_no'];
		return $result;
	}
	
	/**
	 * �ֶ��˿�ύ�˿�����
	 * @param string $repay_type �˿����� recharge
	 * @param int $user_id �û�ID
	 * @param double $amount �˿���
	 * @param array $more_info
	 * @return array
	 * @tutorial
	 * array(
	 * 'recharge_id' => 0,	//��ֵID
	 * 'payment_no' => '', //��ֵ��֧����
	 * 'third_code' => '', //֧����ʽ manual
	 * 'third_oid' => '',	//֧����ˮ��
	 * 'real_name' => '',	//��ʵ����
	 * 'third_buyer' => '',	//�˺�
	 * 'remark' => '', //��ע
	 * )
	 */
	public function manual_repay($repay_type, $user_id, $amount, $more_info=array())
	{
		$result = array();
		
		$repay_type = trim($repay_type);
		$user_id = intval($user_id);
		$amount = number_format($amount*1, 2, '.', '')*1;
		$recharge_id = intval($more_info['recharge_id']);
		$third_code = trim($more_info['third_code']);
		$payment_no = trim($more_info['payment_no']);
		$third_oid = trim($more_info['third_oid']);
		$real_name = trim($more_info['real_name']);
		$third_buyer = trim($more_info['third_buyer']);
		$remark = trim($more_info['remark']);
		
		//������
		if( !in_array($repay_type, array('recharge')) || $user_id<1 || $amount<=0 || $recharge_id<1 || strlen($third_code)<1 )
		{
			$result['error'] = 10;
			$result['message'] = '��������';
			return $result;
		}
		
		$subject = '';
		if( $repay_type=='recharge' )
		{
			$subject = 'Ǯ����ֵ';
		}
		
		$repay_obj = POCO::singleton('ecpay_pai_repay_class');
		$repay_info = array(
			'repay_type' => $repay_type,
			'user_id' => $user_id,
			'amount' => $amount,
			'third_code' => $third_code, 
			'payment_no' => $payment_no,
			'recharge_id' => $recharge_id,
			'subject' => $subject,
			'remark' => $remark,
			'third_oid' => $third_oid,
			'third_buyer' => $third_buyer,
			'real_name' => $real_name,
		);
		$repay_id = $repay_obj->submit_repay($repay_info);
		if( $repay_id<1 )
		{
			$result['error'] = 20;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//��־ http://yp.yueus.com/logs/201502/03_pai_payment.txt
		pai_log_class::add_log(array('repay_id'=>$repay_id), 'manual_repay', 'pai_payment');
		
		$result['error'] = 0;
		$result['message'] = '�ɹ�';
		$result['repay_id'] = $repay_id;
		return $result;
	}
	
	/**
	 * �Ա�������֧��
	 * @param int $date_id
	 * @param int $enroll_id
	 * @return boolean
	 */
	public function pay_date_enroll($date_id,$enroll_id)
	{
		$date_id   = intval($date_id);
		$enroll_id = intval($enroll_id);
		if( $enroll_id < 1 || $date_id < 1 )
		{
			$result['error'] = 10;
			$result['message'] = 'Լ��ID����ID����';
			return $result;
		}
		$enroll_obj  		= POCO::singleton('event_enroll_class');
		$pai_recharge_obj   = POCO::singleton('ecpay_pai_recharge_class');
		$enroll_info 		= get_enroll_by_enroll_id($enroll_id);
		if( empty( $enroll_info ) ){

			$result['error']   = 20;
			$result['message'] = '�Ҳ���enroll_id��Ӧ������';
			return $result;

		}
		$search_arr 	= array( 'date_id'=>$date_id,'status'=>1 );
		$recharge_info  = $pai_recharge_obj->get_recharge_info_by_search($search_arr);
		
		//�Ż�ȯ
		if($enroll_info['is_use_coupon'])
		{
			$total_cost = $enroll_info['original_price']*1;
			$discount_price = $enroll_info['discount_price']*1;
			$pending_cost = $total_cost - $discount_price;
			if( $pending_cost<=0 )
			{
				$result['error']   = 20;
				$result['message'] = '����������';
				return $result;
			}
		}
		else
		{
			$total_cost = $enroll_obj->get_enroll_cost($enroll_id);
			$discount_price = 0;
			$pending_cost = $total_cost;
		}
		
		if( empty($recharge_info ) ){
			//�����ڳ�ֵ  ����ȫΪ���֧��
			$is_balance = 1;
			$is_third   = 0; 

		}
		else{
			if($pending_cost>$recharge_info['amount']){
				//֧�������ڳ�ֵ���  ��Ϊ���֧���͵�����֧������һ����
				$is_balance = 1;
				$is_third   = 1; 

			}
			else{
				//�����������ȫ֧��
				$is_balance = 0;
				$is_third   = 1; 
			}
		}
		$available_balance  = $this->get_user_available_balance($enroll_info['user_id']);
		if( bccomp($available_balance, $pending_cost, 2)==-1 )
		{
			$result['error'] = 20;
			$result['message'] = '�˻���������';
			return $result;
		}
		$trade_ret = $this->submit_trade_out(
			$enroll_info['event_id'],
			$enroll_info['enroll_id'],
			$enroll_info['user_id'],
			$total_cost,
			$discount_price,
			array(
					
				'is_balance' => $is_balance,
				'is_third'	 => $is_third,
				'recharge_id'=> $recharge_info['recharge_id']
				
			)
		);
		if( $trade_ret['error']!==0 )
		{
			return $trade_ret;
		}
		$result['error']   = 0;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * �Ա�������֧��
	 * @param  string 	  $payment_no
	 * @return boolean
	 */
	public function pay_enroll($payment_no)
	{
		$result 	  = array();
		$payment_info = $this->get_payment_info($payment_no);
		if( empty($payment_info) )
		{

			$result['error']   = 10;
			$result['message'] = '�Ƿ���payment_no';
			return $result;

		}
		//����ִ�л����
		$channel_param = trim($payment_info['channel_param']);
		if( strlen($channel_param)<1 )
		{
			$result['error']   = 20;
			$result['message'] = 'channel_param��ʽ����';
			return $result;
		}
		$channel_param_arr  = unserialize($channel_param);
		$event_id           = intval($channel_param_arr['event_id']);
		$enroll_id_str      = trim($channel_param_arr['enroll_id_str']);
		$event_id 	   		= intval($event_id);
		$enroll_id_str 		= trim($enroll_id_str);
		$enroll_id_arr 		= explode(',', $enroll_id_str);
		if( $event_id<1 || empty($enroll_id_arr) )
		{
			$result['error']   = 30;
			$result['message'] = '�ID����ID����';
			return $result;
		}
		$enroll_obj  		= POCO::singleton('event_enroll_class');
		$enroll_cost_detail = $enroll_obj->get_enroll_cost_by_arr( $enroll_id_arr,0 );
		if( empty($enroll_cost_detail) )
		{
			$result['error'] 	= 0;
			$result['message']  = 'û����Ҫ����ı���';
			return $result;
		}
		$list 		 		= $enroll_cost_detail['list'];
		$total_cost  		= $enroll_cost_detail['total_cost'];
		$total_discount  	= $enroll_cost_detail['total_discount']; //�����Żݽ��
		$user_id     		= get_relate_yue_id($list[0]['user_id']);

		$total_pending      = bcsub($total_cost, $total_discount, 2);
		if( $total_pending<=0 )
		{
			$result['error'] 	= 0;
			$result['message']  = 'û����Ҫ����ı���';
			return $result;
		}
		
		if( empty($list) )
		{
			$result['error'] = 0;
			$result['message'] = 'û����Ҫ����ı���';
			return $result;
		}
		$available_balance = $this->get_user_available_balance($user_id);
		if( bccomp($available_balance, $total_pending, 2)==-1 )
		{
			$result['error'] = 40;
			$result['message'] = '�˻���������';
			return $result;
		}
		if( $total_pending > $payment_info['amount'] ){
			//���������֧���ܶ����֧����֧���ɹ����ܶ�  ˵������ȫ��֧�� ����һ������һ���ֵ�����֧��
			$is_balance = 1;
			$is_third   = 1; 

		}
		else{
			//�����������ȫ֧��
			$is_balance = 0;
			$is_third   = 1; 

		}
		$recharge_id    = $payment_info['channel_rid'];
		//����㹻��ֱ�Ӳ�������
		$rel_data   = array('event_id'=>$event_id,'user_id'=>$user_id,'is_balance'=>$is_balance,'is_third'=>$is_third,'recharge_id'=>$recharge_id);
		$submit_ret = $this->batch_submit_trade_out( $list,$rel_data );
		if( $submit_ret > 0 ){
		
			$result['error']   	   = 0;
			$result['trade_succ']  = $submit_ret;
			$result['message'] 	   = '�ɹ�';
			return $result;
		
		}
		else{

			$result['error']   		= 50;
			$result['message'] 		= '���뽻�ױ�ʧ��';
			return $result;

		}

	}

	/**
	 * ����ѻ����֧����app��ѻ������㹻�������֧��
	 * @param int 	 $event_id
	 * @param string $enroll_id_arr ����ID����
	 * @return boolean
	 */
	public function pay_enroll_by_balance($event_id, $enroll_id_arr){

		if( $event_id<1 ){

		 	trace("�Ƿ����� event_id",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;	

		}
		if( empty($enroll_id_arr) || !is_array($enroll_id_arr)){

 			trace("�Ƿ����� enroll_id_arr",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;

		}
		$enroll_obj  = POCO::singleton('event_enroll_class');
		$enroll_cost_detail = $enroll_obj->get_enroll_cost_by_arr( $enroll_id_arr,0 );
		$list 		 = $enroll_cost_detail['list'];
		$total_cost  = $enroll_cost_detail['total_cost'];
		$is_balance  = 1;
		$is_third    = 0;
		$recharge_id = 0;
		$user_id     = get_relate_yue_id($list[0]['user_id']);
		if( empty($list) )
		{
			$result['error']   = 0;
			$result['message'] = '����ID�Ƿ�';
			return $result;
		}
		$rel_data   = array('event_id'=>$event_id,'user_id'=>$user_id,'is_balance'=>$is_balance,'is_third'=>$is_third,'recharge_id'=>$recharge_id);
		$submit_ret = $this->batch_submit_trade_out( $list,$rel_data );
		if( $submit_ret > 0 ){
		
			$result['error']   	   = 0;
			$result['trade_succ']  = $submit_ret;
			$result['message'] 	   = '�ɹ�';
			return $result;
		
		}
		else{

			$result['error']   		= 50;
			$result['message'] 		= '���뽻�ױ�ʧ��';
			return $result;

		}

	}

	/**
	 *  ͨ�������������������ύ����
	 *  
	 */
	public function batch_submit_trade_out( $list,$rel_data )
	{
		$enroll_obj  = POCO::singleton('event_enroll_class');
		if( empty($list) || !is_array($list)){

 			trace("�Ƿ����� list",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;

		}
		if( empty($rel_data) ){

			trace("�Ƿ����� rel_data",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;

		}
		foreach($list as $k=>$v )
		{
			//�����Żݽ��
			$discount_price = $v['discount_price']*1;
			
			$trade_ret = $this->submit_trade_out(
				$rel_data['event_id'],
				$v['enroll_id'],
				$rel_data['user_id'],
				$v['cost'],
				$discount_price,
				array(
					
					'is_balance' => $rel_data['is_balance'],
					'is_third'	 => $rel_data['is_third'],
					'recharge_id'=> $rel_data['recharge_id']
				
				)
			);
			if( $trade_ret['error']!==0 )
			{
				break;
			}
			else
			{
				//�޸ı���״̬
				$ret = $enroll_obj->update_enroll_pay_status( $v['enroll_id'], 1 );
				$ret && $affect_rows++;

			}

		}
		return $affect_rows;

	}
	
	/**
	 * �ύ���ף������ᣨ��Ӱʦ��
	 * @param int $event_id �ID
	 * @param int $enroll_id ����ID
	 * @param int $user_id �û�ID
	 * @param double $total_amount �������룬����2λС�����ܽ��
	 * @param double $discount_amount �������룬����2λС�����Żݽ��
	 * @param array array('is_balance'=>0, 'is_third'=>0, 'recharge_id'=>0, 'subject'=>'', 'remark'=>'')
	 * @return array
	 */
	public function submit_trade_out($event_id, $enroll_id, $user_id, $total_amount, $discount_amount, $more_info=array())
	{
		$event_id = intval($event_id);
		if( !is_array($more_info) ) $more_info = array();
		
		$details_obj = POCO::singleton('event_details_class');
		$event_info  = $details_obj->get_event_by_event_id($event_id);
		$subject = trim($event_info['title']);
		$type_icon = trim($event_info['type_icon']);
		$org_user_id = intval($event_info['org_user_id']);
		
		$channel_module = $this->get_channel_module_by_type_icon($type_icon);
		$more_info['org_user_id'] = $org_user_id;
		if( !isset($more_info['subject']) )
		{
			$more_info['subject'] = $subject;
		}
		
		return $this->submit_trade_out_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $discount_amount, $more_info);
	}
	
	
	/**
	 * �ύ֧�����ף�=>��֧��=>�Ѷ��ᣩ
	 * @param string $channel_module yuepaiԼ��  waipai����  task_request����  task_coin���⿨
	 * @param int $event_id �ID������ID
	 * @param int $enroll_id ����ID������ID
	 * @param int $user_id �û�ID
	 * @param double $total_amount �������룬����2λС�����ܽ��
	 * @param double $discount_amount �������룬����2λС�����Żݽ��
	 * @param array $more_info array('org_user_id'=>0, 'is_balance'=>0, 'is_third'=>0, 'recharge_id'=>0, 'balance_appoint_str'=>'', 'subject'=>'', 'remark'=>'')
	 * @return array
	 * @tutorial
	 *
	 * balance_appoint_str ָ������˻���Ĭ�����У����ֵʱ�����߷ָ���consume���ѽ�  purseǮ�����
	 *
	 */
	public function submit_trade_out_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $discount_amount, $more_info=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->submit_trade_out($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $discount_amount, $more_info);
	}
	
	/**
	 * �ύ���ף�ģ�أ���֯�ߣ�
	 * @param int $event_id �ID
	 * @param int $enroll_id ����ID��ģ�أ���֯��
	 * @param int $user_id �û�ID
	 * @param double $total_amount �������룬����2λС�����ܽ��
	 * @param array $more_info array('discount_amount'=>0.00, 'service_amount'=>0.00, 'org_user_id'=>0, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>'')
	 * @return array
	 */
	public function submit_trade_in($event_id, $enroll_id, $user_id, $total_amount, $more_info=array())
	{
		$event_id = intval($event_id);
		if( !is_array($more_info) ) $more_info = array();
		
		$details_obj = POCO::singleton('event_details_class');
		$event_info  = $details_obj->get_event_by_event_id($event_id);
		$subject = trim($event_info['title']);
		$type_icon = trim($event_info['type_icon']);
		
		$channel_module = $this->get_channel_module_by_type_icon($type_icon);
		if( !isset($more_info['subject']) )
		{
			$more_info['subject'] = $subject;
		}
		
		//����ֳɽ�����ѣ�
		/*
		$pai_score_obj = POCO::singleton('pai_score_class');
		$level_info = $pai_score_obj->get_user_score_level($user_id);
		$commission_obj = POCO::singleton('ecpay_pai_commission_class');
		$service_amount = $commission_obj->cal_trade_commission($user_id, $level_info['level'], $total_amount);
		$more_info['service_amount'] = $service_amount;
		*/
		
		return $this->submit_trade_in_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info);
	}
	
	/**
	 * �ύ���뽻�ף�=>��֧����
	 * @param string $channel_module
	 * @param int $event_id �ID
	 * @param int $enroll_id ����ID��ģ�أ���֯��
	 * @param int $user_id �û�ID
	 * @param double $total_amount �������룬����2λС�����ܽ��
	 * @param array $more_info array('discount_amount'=>0.00, 'service_amount'=>0.00, 'org_user_id'=>0, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>'')
	 * @return array
	 */
	public function submit_trade_in_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->submit_trade_in($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info);
	}
	
	/**
	 * �ύ���ף������˿�˻�����Ӱʦ��
	 * @param int $event_id �ID
	 * @param int $enroll_id ����ID��ģ�أ���֯��
	 * @param int $user_id �û�ID
	 * @param double $total_amount �������룬����2λС�����ܽ��
	 * @param array $more_info array('org_user_id'=>0, 'is_balance'=>0, 'is_third'=>0, 'recharge_id'=>0, 'subject'=>'', 'remark'=>'')
	 * @return array
	 */
	public function submit_trade_refund($event_id, $enroll_id, $user_id, $total_amount, $more_info=array())
	{
		$event_id = intval($event_id);
		if( !is_array($more_info) ) $more_info = array();
		
		$details_obj = POCO::singleton('event_details_class');
		$event_info  = $details_obj->get_event_by_event_id($event_id);
		$subject = trim($event_info['title']);
		$type_icon = trim($event_info['type_icon']);
		
		$channel_module = $this->get_channel_module_by_type_icon($type_icon);
		if( !isset($more_info['subject']) )
		{
			$more_info['subject'] = $subject;
		}
		
		return $this->submit_trade_refund_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info);
	}
	
	/**
	 * �ύ�˿�ף�=>��֧����
	 * @param string $channel_module
	 * @param int $event_id �ID������ID
	 * @param int $enroll_id ����ID������ID��ģ�أ���֯��
	 * @param int $user_id �û�ID
	 * @param double $total_amount �������룬����2λС�����ܽ��
	 * @param array $more_info array('org_user_id'=>0, 'is_balance'=>0, 'is_third'=>0, 'recharge_id'=>0, 'subject'=>'', 'remark'=>'')
	 * @return array
	 */
	public function submit_trade_refund_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->submit_trade_refund($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info);
	}
	
	/**
	 * ��ȡ������Ϣ�����ݱ���ID
	 * @param int $enroll_id
	 * @return array
	 */
	public function get_trade_info_by_enroll_id($enroll_id)
	{
		$channel_module = $this->get_channel_module_by_enroll_id($enroll_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_trade_info_by_apply_id($channel_module, $enroll_id);
	}
	
	/**
	 * ���ύ��
	 * ֧���ߣ���Ӱʦ�����Ѷ��ᣬ֧�����˻�����Ӧ�����
	 * �����ߣ�ģ�ء���֯�ߣ����Ѷ��ᣬ��˻�����Ӧ�����
	 * @param int $trade_id
	 * @return boolean
	 */
	public function frozen_trade($trade_id)
	{
		//$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		//return $trade_obj->frozen_trade($trade_id);
		return false;
	}
	
	/**
	 * ֧���ɹ��Ļص�
	 * ���֧��״̬������ֵ״̬
	 * @param array $payment_info
	 * @return array
	 */
	public function return_recharge($payment_info)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		$recharge_info = $recharge_obj->get_recharge_info($payment_info['channel_rid']);
		return $recharge_obj->return_recharge($payment_info, $recharge_info);
	}
	
	/**
	 * ֧���ɹ��Ļص�
	 * ���֧��״̬������ֵ״̬��ִ�г�ֵ
	 * @param array $payment_info
	 * @return array
	 */
	public function notify_recharge($payment_info)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->approve_recharge_by_payment_info($payment_info);
	}
	
	/**
	 * �رս��ף�ȡ������
	 * ��֧�����ѹر�
	 * ֧�����Ѷ��᣺�ѹرգ�֧�����˻�����Ӧ�����
	 * �������Ѷ��᣺�ѹرգ���˻�����Ӧ�����
	 * @param int $enroll_id
	 * @return boolean
	 */
	public function closed_trade($enroll_id)
	{
		$channel_module = $this->get_channel_module_by_enroll_id($enroll_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->closed_trade_by_apply_id($channel_module, $enroll_id);
	}
	
	/**
	 * �رս��ף�ȡ������
	 * ��֧�����ѹر�
	 * ֧�����Ѷ��᣺�ѹرգ�֧�����˻�����Ӧ�����
	 * �������Ѷ��᣺�ѹرգ���˻�����Ӧ�����
	 * @param string $channel_module
	 * @param int $enroll_id
	 * @return boolean
	 */
	public function closed_trade_v2($channel_module, $enroll_id)
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->closed_trade_by_apply_id($channel_module, $enroll_id);
	}
	
	/**
	 * �ȷ��
	 * ��Ӱʦ��������֧������Ӱʦ���ת������
	 * ��֯�ߣ������Ѷ��ᣬ���ӻӦ�����
	 * ȷ��ǰ����Ҫ���򲹵ı���ȡ������֤���֧ƽ��
	 * @param int $event_id
	 * @return array
	 */
	private function confirm_event($event_id)
	{
		$channel_module = $this->get_channel_module_by_event_id($event_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->confirm_event($channel_module, $event_id);
	}
	
	/**
	 * ȡ���
	 * �ȴ��������ߣ���Ǯ�ⶳ���˻ػ�˻�
	 * �ٴ���֧���ߣ���Ǯ�ⶳ���˻�֧�����˻�
	 * @param int $event_id
	 * @return array
	 */
	public function cancel_event($event_id)
	{
		$channel_module = $this->get_channel_module_by_event_id($event_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->cancel_event($channel_module, $event_id);
	}
	
	/**
	 * ȡ���
	 * �ȴ��������ߣ���Ǯ�ⶳ���˻ػ�˻�
	 * �ٴ���֧���ߣ���Ǯ�ⶳ���˻�֧�����˻�
	 * @param string $channel_module
	 * @param int $event_id
	 * @return array
	 */
	public function cancel_event_v2($channel_module, $event_id)
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->cancel_event($channel_module, $event_id);
	}
	
	/**
	 * �����
	 * �ȴ���֧���ߣ�����齻��״̬
	 * �ٴ��������ߣ���Ǯת��������
	 * @param int $event_id
	 * @return array
	 */
	private function finish_event($event_id)
	{
		$channel_module = $this->get_channel_module_by_event_id($event_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		$ret = $trade_obj->finish_event($channel_module, $event_id);
		
		//���û���ϵͳ���������ظ�����
		if( $ret['error']===0 )
		{
			$pai_score_obj = POCO::singleton('pai_score_class');
			$trade_list = $trade_obj->get_trade_list($channel_module, $event_id, false, "is_carryover=1 AND status=" . ecpay_pai_trade_v2_class::STATUS_PAID, 'trade_id ASC', '0,99999999');
			foreach ($trade_list as $trade_info )
			{
				$trade_id = intval($trade_info['trade_id']);
				$user_id = intval($trade_info['user_id']);
				$trade_type = trim($trade_info['trade_type']);
				$total_amount = trim($trade_info['total_amount']);
				if( $trade_type=='out' )
				{
					$pai_score_obj->add_operate_queue($user_id, 'consume', $total_amount, "trade_id:{$trade_id}");
				}
				elseif( $trade_type=='in' )
				{
					$pai_score_obj->add_operate_queue($user_id, 'income', $total_amount, "trade_id:{$trade_id}");
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * ���������ɻ��
	 * @param int $event_id �ID
	 * @param array $refund_list �˿��б�  array( array('user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $in_list �����б�  array( array('discount_amount'=>0.00, 'user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $coupon_refund_list �Ż�ȯ�˿��б� array( array('id'=>0), )
	 * @param array $coupon_cash_list �Żݶ����б� array( array('id'=>0, 'user_id'=>0, 'org_user_id'=>0, 'amount'=>0.00, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>''), )
	 * @return array
	 */
	public function end_event($event_id, $refund_list, $in_list, $coupon_refund_list=array(), $coupon_cash_list=array())
	{
		$channel_module = $this->get_channel_module_by_event_id($event_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		$ret = $trade_obj->end_event($channel_module, $event_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
		
		//���û���ϵͳ���������ظ�����
		if( $ret['error']===0 )
		{
			$pai_score_obj = POCO::singleton('pai_score_class');
			$trade_list = $trade_obj->get_trade_list($channel_module, $event_id, false, "is_carryover=1 AND status=" . ecpay_pai_trade_v2_class::STATUS_PAID, 'trade_id ASC', '0,99999999');
			foreach ($trade_list as $trade_info )
			{
				$trade_id = intval($trade_info['trade_id']);
				$user_id = intval($trade_info['user_id']);
				$trade_type = trim($trade_info['trade_type']);
				$total_amount = trim($trade_info['total_amount']);
				if( $trade_type=='out' )
				{
					$pai_score_obj->add_operate_queue($user_id, 'consume', $total_amount, "trade_id:{$trade_id}");
				}
				elseif( $trade_type=='in' )
				{
					$pai_score_obj->add_operate_queue($user_id, 'income', $total_amount, "trade_id:{$trade_id}");
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * ���������ɻ��
	 * @param string $channel_module
	 * @param int $event_id �ID
	 * @param array $refund_list �˿��б�  array( array('user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $in_list �����б�  array( array('discount_amount'=>0.00, 'user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $coupon_refund_list �Ż�ȯ�˿��б� array( array('id'=>0), )
	 * @param array $coupon_cash_list �Żݶ����б� array( array('id'=>0, 'user_id'=>0, 'org_user_id'=>0, 'amount'=>0.00, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>''), )
	 * @return array
	 */
	public function end_event_v2($channel_module, $event_id, $refund_list, $in_list, $coupon_refund_list=array(), $coupon_cash_list=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->end_event($channel_module, $event_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
	}
	
	/**
	 * ��ȡ���24Сʱ�����ѽ������ж��Ƿ���Ҫ���ֻ���֤��
	 * @param int $user_id
	 * @return double
	 */
	private function get_trade_amount_by_user_id($user_id)
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_trade_amount_by_user_id($user_id);
	}
	
	/**
	 * ��ȡ������δ������
	 * @param int $org_user_id
	 * @param string $channel_module yuepaiԼ�� waipai����
	 * @return double
	 */
	public function get_unsettle_org_amount($org_user_id, $channel_module='')
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_unsettle_org_amount($org_user_id, $channel_module);
	}
	
	/**
	 * ��ȡ�������ѽ�����
	 * @param int $settle_id
	 * @param string $channel_module yuepaiԼ�� waipai����
	 * @return double
	 */
	public function get_settle_org_amount($settle_id, $channel_module='')
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_org_amount($settle_id, $channel_module);
	}
	
	/**
	 * ��ȡ�������ѽ�����
	 * @param int $settle_id
	 * @param string $channel_module yuepaiԼ�� waipai����
	 * @return array
	 */
	public function get_settle_org_amount_info($settle_id, $channel_module='')
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_org_amount_info($settle_id, $channel_module);
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
	public function get_unsettle_trade_list($org_user_id, $channel_module='', $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_unsettle_trade_list($org_user_id, $channel_module, $b_select_count, $where_str, $order_by, $limit, $fields);
	}
	
	/**
	 * ��ȡ������δ����ID
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $where_str
	 * @return array
	 */
	public function get_unsettle_trade_event_id_arr($org_user_id, $channel_module='', $where_str='')
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_unsettle_trade_event_id_arr($org_user_id, $channel_module, $where_str);
	}
	
	/**
	 * ��ȡ����������Ϣ
	 * @param int $settle_id
	 * @return array
	 */
	public function get_settle_info($settle_id)
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_info($settle_id);
	}
	
	/**
	 * ��ȡ�������ѽ����б�
	 * @param int $org_user_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_settle_list($org_user_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_list($org_user_id, $b_select_count, $where_str, $order_by, $limit, $fields);
	}
	
	/**
	 * ��ȡ�������ѽ���Ĺ����б�
	 * @param int $org_user_id
	 * @param int $settle_id
	 * @param string $channel_module
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_settle_ref_trade_list($org_user_id, $settle_id=0, $channel_module='', $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_ref_trade_list($org_user_id, $settle_id, $channel_module, $b_select_count, $where_str, $order_by, $limit, $fields);
	}
	
	/**
	 * �����û���������������
	 * @param int $user_id
	 * @param double $amount
	 * @param int $date_id
	 * @return boolean
	 */
	public function frozen_date($user_id,$amount,$date_id)
	{
		$date_obj 			= POCO::singleton('ecpay_pai_date_class');
		$pai_recharge_obj   = POCO::singleton('ecpay_pai_recharge_class');
		$search_arr 	= array( 'date_id'=>$date_id,'status'=>1 );
		$recharge_info  = $pai_recharge_obj->get_recharge_info_by_search($search_arr);
		if( empty($recharge_info ) ){
			//�����ڳ�ֵ  ����ȫΪ���֧��
			$is_balance = 1;
			$is_third   = 0; 
			$subject = 'Լ������';
		}
		else{
			if($amount>$recharge_info['amount']){
				//֧�������ڳ�ֵ���  ��Ϊ���֧���͵�����֧������һ����
				$is_balance = 1;
				$is_third   = 1; 

			}
			else{
				//�����������ȫ֧��
				$is_balance = 0;
				$is_third   = 1; 
			}
			$subject = trim($recharge_info['subject']);
		}
		$more_info = array(
		
			'is_balance' =>$is_balance,
			'is_third'	 =>$is_third,
			'recharge_id'=>$recharge_info['recharge_id'],
			'subject'	 =>$subject,
			'remark'	 =>$recharge_info['remark']

		);
		return $date_obj->frozen_date($user_id, $amount, $date_id,$more_info);

	}
	
	/**
	 * �ⶳ�û���������������
	 * @param int $user_id
	 * @param double $amount
	 * @param int $date_id
	 * @return boolean
	 */
	public function unfrozen_date($user_id, $amount, $date_id)
	{
		//$date_obj = POCO::singleton('ecpay_pai_date_class');
		//return $date_obj->unfrozen_date($user_id, $amount, $date_id);
		return false;
	}

	/**
	 * ģ�ؾܾ�����
	 * @param int $date_id
	 * @return boolean
	 */
	public function refused_date($date_id){

		$date_obj = POCO::singleton('ecpay_pai_date_class');
		return $date_obj->refused_date($date_id);

	}

	/**
	 * ģ�ؽ�������
	 * @param int $date_id
	 * @return boolean
	 */
	public function accepted_date($date_id){

		$date_obj = POCO::singleton('ecpay_pai_date_class');
		return $date_obj->accepted_date($date_id);

	}

	/**
	 * ��������
	 * @param string $withdraw_type Ǯ������ withdraw  ���ý����� bail
	 * @param int $user_id
	 * @param double $amount
	 * @param string $real_name
	 * @param string $third_type alipay֧���� tenpay�Ƹ�ͨ
	 * @param string $third_account
	 * @return int -1Ϊ�˻��޿����ֵ���� -2Ϊ�û����� 0Ϊ������ ��������Ϊ����ID
	 */
	public function submit_withdraw($withdraw_type,$user_id,$amount,$real_name,$third_type,$third_account)
	{
		//��ֹ����ģ�ء������̼�����
		$pai_model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');
		$pai_model_relate_org_ret = $pai_model_relate_org_obj->get_org_info_by_user_id($user_id);
		if( $pai_model_relate_org_ret )
		{
			return 0;
		}
		
		if( $withdraw_type == 'withdraw' )
		{
			$subject = 'Ǯ������';
		}
		elseif( $withdraw_type == 'bail' )
		{
			$subject = '���ý�����';
		}
		$data = array(
			'subject'=>$subject,
			'withdraw_type'=>$withdraw_type,
			'amount' => $amount,
			'real_name'=>$real_name,
			'third_account' => $third_account,
			'user_id' => $user_id,
			'service_fee' => 0,
			'remark' => '',
		);
		$ecpay_pai_withdraw_obj = POCO::singleton('ecpay_pai_withdraw_class');
		$withdraw_id = $ecpay_pai_withdraw_obj->submit_withdraw($data);
		return $withdraw_id;
	}
	
	/**
	 * ��ȡ�����б�
	 * @param string $search_arr   ��ѯ�������� array('user_id'=>'','event_id'=>,'status'=>'','is_carryover'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
	 * @return array|int
	 */
	public function get_trade_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'trade_id DESC')
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_trade_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * ��ȡ��ֵ�б�
	 * @param string $search_arr   ��ѯ�������� array('user_id'=>'','recharge_type'=>'','third_code'=>,'status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
	 * @return array|int
	 */
	public function get_recharge_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'recharge_id DESC')
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * ��ȡ�����б�
	 * @param string $search_arr   ��ѯ�������� array('user_id'=>'','status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
	 * @return array|int
	 */
	public function get_withdraw_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'withdraw_id DESC')
	{
		$withdraw_obj = POCO::singleton('ecpay_pai_withdraw_class');
		return $withdraw_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * ��ȡת���б�
	 *
	 * @param string $search_arr    ��ѯ�������� array('user_id'=>'','status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
	 * @return array|int
	 */
	public function get_transfer_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'transfer_id DESC')
	{
		$transfer_obj = POCO::singleton('ecpay_pai_transfer_class');
		return $transfer_obj->get_transfer_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * ��ȡ�˻��б�
	 * @param string $search_arr    ��ѯ�������� array('user_id'=>'','status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
	 * @return array|int
	 */
	public function get_repay_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'repay_id DESC')
	{
		$repay_obj = POCO::singleton('ecpay_pai_repay_class');
		return $repay_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * ��ȡ�˵������б�
	 * @param int $user_id
	 * @param string $b_select_count
	 * @param string $limit
	 * @return array array(
	 * 		array(
	 * 			'subject' => '', //����
	 * 			'is_invalid' => 0, //�Ƿ���Ч��0��Ч��1��Ч
	 * 			'flow_type' => 0, //���ţ�0������1����
	 * 			'amount' => 0, //���
	 * 			'status' => '', //״̬
	 * 			'remark' => '', //��ע
	 * 			'repay_str_arr' => array(), //�˿�
	 * 			'add_date' => '', //����
	 * 		)
	 * )
	 */
	public function get_bill_trade_list($user_id, $b_select_count=false, $limit='0,10', $order_by='trade_id DESC')
	{
		$bill_list= array();
		
		//������
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $bill_list;
		}
		
		//��ѯ
		$search_arr = array(
			'user_id' => $user_id,
		);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		$trade_list = $trade_obj->get_trade_list_by_search($search_arr, $b_select_count, $limit, $order_by);
		if( $b_select_count )
		{
			return $trade_list;
		}
		
		include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
		$pai_coupon_obj = POCO::singleton('pai_coupon_class');
		
		//��������
		$trade_type_arr = array(
			'in' => '����',
			'out' => '֧��',
			'refund' => '�˿�',
			'transfer' => 'ת��',
		);
		$status_arr = array(
			0 => '�ȴ�֧��',
			7 => '���׹ر�',
			8 => '���׳ɹ�',
		);
		foreach($trade_list as $trade_info)
		{
			$trade_id = intval($trade_info['trade_id']);
			$channel_module = trim($trade_info['channel_module']);
			$apply_id = intval($trade_info['apply_id']);
			$event_id = intval($trade_info['event_id']);
			$subject = trim($trade_info['subject']);
			$trade_type = trim($trade_info['trade_type']);
			$flow_type = trim($trade_info['flow_type']);
			$status = intval($trade_info['status']);
			$total_amount = trim($trade_info['total_amount']);
			$discount_amount = trim($trade_info['discount_amount']);
			$pending_amount = trim($trade_info['pending_amount']);
			$add_time = intval($trade_info['add_time']);
			
			//����
			$trade_type_name = trim($trade_type_arr[$trade_type]);
			if( strlen($trade_type_name)<1 ) $trade_type_name = 'δ֪';
			if( strlen($subject)>0 )
			{
				$subject = $trade_type_name . '-' . $subject;
			}
			else
			{
				$subject = $trade_type_name;
			}
			
			//״̬
			$status_str = trim($status_arr[$status]);
			if( strlen($status_str)<1 ) $status_str = '������';
			
			//������
			$repay_str_arr = array();
			$amount = 0;
			$remark = ''; //��ע
			if( $trade_type=='in' ) //����
			{
				$coupon_event_amount = $pai_coupon_obj->sum_ref_event_cash_amount_by_event_id($channel_module, $event_id); //�ܲ������
				$coupon_in_amount = $pai_coupon_obj->sum_ref_order_in_amount_by_in_user_id($channel_module, $event_id, $user_id); //�����߲������
				$amount = $pending_amount + $coupon_in_amount;
				$none_amount = $discount_amount - $coupon_event_amount; //�������
				if( $none_amount>0 )
				{
					$none_amount = number_format($none_amount, 2, '.', '');
					$remark = "�˵���{$total_amount}���Żݽ��֧����-{$none_amount}";
				}
			}
			elseif( $trade_type=='out' ) //֧��
			{
				if( $status==7 ) //�ر�
				{
					$repay_str_arr = $this->get_bill_repay_str_arr($user_id, $trade_id, $pending_amount);
				}
				$amount = $pending_amount;
				if( $discount_amount>0 )
				{
					$remark = "�˵���{$total_amount}���Żݽ�{$discount_amount}";
				}
			}
			elseif( $trade_type=='refund' ) //�˿�
			{
				$out_trade_info = $trade_obj->get_trade_info_by_apply_id($channel_module, $apply_id);
				$repay_str_arr = $this->get_bill_repay_str_arr($user_id, $out_trade_info['trade_id'], $pending_amount);
				$amount = $pending_amount;
			}
			elseif( $trade_type=='transfer' )
			{
				$amount = $pending_amount;
			}
			else
			{
				$amount = $pending_amount;
			}
			
			//���ķ��ţ�0������1����
			$flow_type_tmp = ($flow_type=='in') ? 0 : 1;
			
			//�Ƿ���Ч
			$is_invalid = ($status==7) ? 1 : 0;
			
			$bill_list[] = array(
				'subject' => $subject,
				'is_invalid' => $is_invalid,
				'flow_type' => $flow_type_tmp,
				'amount' => number_format($amount, 2, '.', ''),
				'status' => $status,
				'status_str' => $status_str,
				'remark' => $remark,
				'repay_str_arr' => $repay_str_arr,
				'add_date' => date('Y.m.d', $add_time),
			);
		}
		
		return $bill_list;
	}
	
	/**
	 * ��ȡ�˵��˿���Ϣ
	 * @param int $user_id
	 * @param int $trade_id
	 * @param double $repay_amount
	 * @return array
	 */
	private function get_bill_repay_str_arr($user_id, $trade_id, $repay_amount)
	{
		$repay_str_arr = array();
		
		$user_id = intval($user_id);
		$trade_id = intval($trade_id);
		$repay_amount = $repay_amount*1;
		if( $user_id<1 || $trade_id<1 || $repay_amount==0 )
		{
			return $repay_str_arr;
		}
		
		$remain_amount = $repay_amount;
		$repay_obj = POCO::singleton('ecpay_pai_repay_class');
		$repay_list = $repay_obj->get_repay_list($user_id, false, "trade_id={$trade_id} AND status IN (0,1)", 'repay_id ASC', '0,999');
		foreach($repay_list as $repay_info)
		{
			$status = intval($repay_info['status']);
			$third_code = trim($repay_info['third_code']);
			$amount = trim($repay_info['amount']);
			
			$status_name = '';
			if( $status==0 )
			{
				$status_name = '�����˻�';
			}
			elseif( $status==1 )
			{
				$status_name = '���˻�';
			}
			
			$third_code_name = '';
			if( in_array($third_code, array('tenpay_wxpub', 'tenpay_wxapp')) )
			{
				$third_code_name = '΢��';
			}
			elseif( in_array($third_code, array('alipay', 'alipay_wap', 'alipay_purse')) )
			{
				$third_code_name = '֧����';
			}
			
			$remain_amount = $remain_amount - $amount;
			
			$repay_str_arr[] = "{$status_name}[{$third_code_name}] {$amount}Ԫ";
		}
		if( $remain_amount>0 )
		{
			$remain_amount = number_format($remain_amount, 2, '.', '');
			$repay_str_arr[] = "���˻�[ԼԼǮ��] {$remain_amount}Ԫ";
		}
		
		return $repay_str_arr;
	}
	
	/**
	 * ��ȡ�˵���ֵ�б�
	 * @param int $user_id
	 * @param string $b_select_count
	 * @param string $limit
	 * @return array
	 */
	public function get_bill_recharge_list($user_id, $b_select_count=false, $limit='0,10', $order_by='recharge_id DESC')
	{
		$bill_list= array();
		
		//������
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $bill_list;
		}
		
		//��ѯ
		$search_arr = array(
			'user_id' => $user_id,
			'recharge_type' => array('recharge', 'bail', 'consume'),
			'status' => 1,
		);
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		$recharge_list = $recharge_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
		if( $b_select_count )
		{
			return $recharge_list;
		}
		
		//��������
		$status_arr = array(
			0 => '�ȴ�֧��',
			1 => '��ֵ�ɹ�',
			2 => '��ֵʧ��',
		);
		foreach($recharge_list as $recharge_info)
		{
			$subject = trim($recharge_info['subject']);
			$amount = trim($recharge_info['amount']);
			$status = intval($recharge_info['status']);
			$add_time = intval($recharge_info['add_time']);
			
			//����
			$recharge_type_name = '��ֵ';
			if( strlen($subject)>0 )
			{
				$subject = $recharge_type_name . '-' . $subject;
			}
			else
			{
				$subject = $recharge_type_name;
			}
			
			//״̬
			$status_str = trim($status_arr[$status]);
			if( strlen($status_str)<1 ) $status_str = '������';
			
			//��ע
			$remark = '';
			
			$bill_list[] = array(
				'subject' => $subject,
				'is_invalid' => 0,
				'flow_type' => 0,
				'amount' => number_format($amount, 2, '.', ''),
				'status' => $status,
				'status_str' => $status_str,
				'remark' => $remark,
				'repay_str_arr' => array(),
				'add_date' => date('Y.m.d', $add_time),
			);
		}
		
		return $bill_list;
	}
	
	/**
	 * ��ȡ�˵������б�
	 * @param int $user_id
	 * @param string $b_select_count
	 * @param string $limit
	 * @return array
	 */
	public function get_bill_withdraw_list($user_id, $b_select_count=false, $limit='0,10', $order_by='withdraw_id DESC')
	{
		$bill_list= array();
		
		//������
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $bill_list;
		}
		
		//��ѯ
		$search_arr = array(
			'user_id' => $user_id,
			'withdraw_type' => array('withdraw', 'bail')
		);
		$withdraw_obj = POCO::singleton('ecpay_pai_withdraw_class');
		$withdraw_list = $withdraw_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
		if( $b_select_count )
		{
			return $withdraw_list;
		}
		
		//��������
		$status_arr = array(
			1 => '���ֳɹ�',
			2 => '����ʧ��',
		);
		foreach($withdraw_list as $withdraw_info)
		{
			$subject = trim($withdraw_info['subject']);
			$amount = trim($withdraw_info['amount']);
			$status = intval($withdraw_info['status']);
			$add_time = intval($withdraw_info['add_time']);
			
			//����
			$recharge_type_name = '����';
			if( strlen($subject)>0 )
			{
				$subject = $recharge_type_name . '-' . $subject;
			}
			else
			{
				$subject = $recharge_type_name;
			}
			
			//״̬
			$status_str = trim($status_arr[$status]);
			if( strlen($status_str)<1 ) $status_str = '������';
			
			//��ע
			$remark = '';
			
			//�Ƿ���Ч
			$is_invalid = ($status==2) ? 1 : 0;
			
			$bill_list[] = array(
				'subject' => $subject,
				'is_invalid' => $is_invalid,
				'flow_type' => 1,
				'amount' => number_format($amount, 2, '.', ''),
				'status' => $status,
				'status_str' => $status_str,
				'remark' => $remark,
				'repay_str_arr' => array(),
				'add_date' => date('Y.m.d', $add_time),
			);
		}
		
		return $bill_list;
	}
	
	/**
	 * ����û��Ƿ�ʹ�ù�֧��ϵͳ��
	 * �����Ƿ�����ת���û���ɫ
	 * @param int $user_id
	 * @return bool trueʹ�ù���falseδʹ��
	 */
	public function check_user_used($user_id)
	{
		$user_id = intval($user_id);
		if ( empty( $user_id ) ) 
        {
            trace("�Ƿ����� user_id ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
        }
        $recharge_obj   = POCO::singleton('ecpay_pai_recharge_class');
        $search_arr     = array('user_id'=>$user_id); 
		$recharge_count = $recharge_obj->get_list_by_search($search_arr,true);
		if( $recharge_count )
			return true;
		$trade_obj   	= POCO::singleton('ecpay_pai_trade_v2_class');
		$trade_count    = $trade_obj->get_trade_list_by_search($search_arr,true);
		if( $trade_count )
			return true;
		$transfer_obj 	= POCO::singleton('ecpay_pai_transfer_class');
		$transfer_count = $transfer_obj->get_transfer_list_by_search($search_arr,true);
		if( $transfer_count )
			return true;
		$date_obj 	= POCO::singleton('ecpay_pai_date_class');
		$date_count = $date_obj->get_list_by_search($search_arr,true);
		if( $date_count )
			return true;
		return false;
		
	}

	/**
	 * ��ȡ��С֧�����
	 * @return boolean
	 */
	public function get_min_pay_amount(){

		return  $this->min_pay_amount;

	}

	/**
	 * ��ȡ���֧�����
	 * @return boolean
	 */
	public function get_max_pay_amount(){

		return  $this->max_pay_amount;

	}
	
	/**
	 * ��ȡ��С֧�����̼�����
	 * @return double
	 */
	public function get_min_seller_withdraw_amount()
	{
		return 10;
	}
	
	/**
	 * ��ȡ���֧�����̼�����
	 * @return double
	 */
	public function get_max_seller_withdraw_amount()
	{
		return 100000;
	}
	
	/**
	 * ��ȡ��ֵ���̼�����
	 * @param int $user_id
	 * @return array
	 */
	public function get_card_seller_info($user_id)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_card_seller_info($user_id);
	}
	
	/**
	 * ��ȡ�Ƿ��ֵ���̼�
	 * @param int $user_id
	 * @return bool
	 */
	public function check_is_card_seller($user_id)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->check_is_card_seller($user_id);
	}
	
	/**
	 * ��ȡ��ֵ����Ϣ
	 * @param string $card_no
	 * @return array
	 */
	public function get_card_info($card_no)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_card_info($card_no);
	}
	
	/**
	 * ��ȡ��ֵ���б�
	 * @param int $user_id
	 * @param int $keyword ���Źؼ���
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit
	 * @param string $fields ��ѯ�ֶ�
	 * @return array|int
	 */
	public function get_card_list_by_seller($user_id, $keyword, $b_select_count=false, $where_str='', $order_by='card_id ASC', $limit='0,20', $fields='*')
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_card_list_by_seller($user_id, $keyword, $b_select_count, $where_str, $order_by, $limit, $fields);
	}
	
	/**
	 * �����ֵ��
	 * @param int $user_id
	 * @param int $card_no
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function enable_card($user_id, $card_no)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->enable_card($user_id, $card_no);
	}
	
	/**
	 * ���ϳ�ֵ��
	 * @param int $user_id
	 * @param int $card_no
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function disable_card($user_id, $card_no)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->disable_card($user_id, $card_no);
	}
	
	/**
	 * ʹ�ó�ֵ����ֵ
	 * @param int $user_id
	 * @param int $card_no
	 * @param int $card_pwd
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function use_card($user_id, $card_no, $card_pwd)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->use_card($user_id, $card_no, $card_pwd);
	}
	
	/**
	 * ȡ����ͳ�����ݺ�ҵ��ϵͳ����
	 * @param int $request_time ����ʱ��
	 * @return array ���ɹ�ʱ���ؿ�����
	 */
	public function collect_daily_sell_stats_report($request_time)
	{
		$report_obj = POCO::singleton('ecpay_pai_report_class');
		return $report_obj->collect_daily_sell_stats_report($request_time);
	}
	
}
