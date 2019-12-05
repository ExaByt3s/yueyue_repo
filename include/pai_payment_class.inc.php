<?php
/**
 * 支付类
 * @author Henry
 */

class pai_payment_class extends POCO_TDG
{
	/**
	 * 渠道标识
	 * @var string
	 */
	private $channel_code = 'pai';

	/**
	 * 渠道模块
	 * @var string
	 */
	private $channel_module = 'recharge';
	
	/**
	 * 用户账户类型
	 * @var string
	 */
	private $user_account_type = 'actual';
	
	/**
	 * 信用金账户类型
	 * @var string
	 */
	private $bail_account_type = 'bail';

	/**
	 * 最小的支付金额
	 * @var int
	 */
	private $min_pay_amount = 10;
	
	/**
	 * 最大的支付金额
	 * @var string
	 */
	private $max_pay_amount = 10000;

	/**
	 * 活动的账户类型
	 * @var string
	 */
	private $event_account_type = 'virtual';
	
	/**
	 * 消费金的账户类型
	 * @var string
	 */
	private $consume_account_type = 'consume';
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		//这里辅助开启实时数据。由于目前只有登录才会实时，没登录状态的异步通知、定时执行可能会出问题。
		define('G_DB_GET_REALTIME_DATA', 1);
		
		if( defined('G_PAI_ECPAY_DEV') ){
			//测试模式
			$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dev_dir');
			include_once $ecpay_app_dir . '/poco_app_common.inc.php';

		}
		else{
			
			$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dir');
			include_once $ecpay_app_dir . '/poco_app_common.inc.php';
		
		}
	}
	
	/**
	 * 获取渠道模块
	 * @param string $type_icon
	 * @return string
	 */
	public function get_channel_module_by_type_icon($type_icon)
	{
		$type_icon = trim($type_icon);
		return ($type_icon=='yuepai_app' ? 'yuepai' : 'waipai');
	}
	
	/**
	 * 获取渠道模块
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
	 * 获取渠道模块
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
	 * 获取钱包账户
	 * @param int $user_id
	 * @return array
	 */
	public function get_purse_account_info($user_id)
	{
		$account_obj = POCO::singleton('ecpay_account_v2_class', array($this->channel_code));
		return $account_obj->get_account_info_by_channel($this->user_account_type, $user_id); //钱包账户
	}
	
	/**
	 * 获取钱包余额（可提现）
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
	 * 获取消费金账户
	 * @param int $user_id
	 * @return array
	 */
	public function get_consume_account_info($user_id)
	{
		$account_obj = POCO::singleton('ecpay_account_v2_class', array($this->channel_code));
		return $account_obj->get_account_info_by_channel($this->consume_account_type, $user_id); //钱包账户
	}
	
	/**
	 * 获取消费金余额（不可提现）
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
	 * 获取用户账户
	 * @param int $user_id
	 * @return array
	 */
	public function get_user_account_info($user_id)
	{
		$user_account_info = $this->get_purse_account_info($user_id); //钱包账户
		if( empty($user_account_info) )
		{
			return array();
		}
		
		//兼容旧代码，合计加上消费金
		$consume_account_info = $this->get_consume_account_info($user_id); //消费金账户
		$user_account_info['balance'] = bcadd($user_account_info['balance'], $consume_account_info['balance'], 2);
		$user_account_info['receivable'] = bcadd($user_account_info['receivable'], $consume_account_info['receivable'], 2);
		$user_account_info['payable'] = bcadd($user_account_info['payable'], $consume_account_info['payable'], 2);
		$user_account_info['available_balance'] = bcadd($user_account_info['available_balance'], $consume_account_info['available_balance'], 2);
		
		return $user_account_info;
	}
	
	/**
	 * 获取用户可用余额
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
	 * 获取活动账户
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
	 * 获取信用金账户
	 * @param int $user_id
	 * @return array
	 */
	public function get_bail_account_info($user_id)
	{
		$account_obj = POCO::singleton('ecpay_account_v2_class', array($this->channel_code));
		return $account_obj->get_account_info_by_channel($this->bail_account_type, $user_id);
	}
	
	/**
	 * 获取信用金可用余额
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
	 * 约约优惠券账户，用户ID
	 * @return int
	 */
	public function get_coupon_user_id()
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_coupon_user_id();
	}
	
	/**
	 * 获取账号统计
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
	 * 获取支付信息
	 * @param string $payment_no
	 * @return array
	 */
	public function get_payment_info($payment_no)
	{
		$payment_obj = POCO::singleton('ecpay_payment_class');
		return $payment_obj->get_payment_info($payment_no);
	}
	
	/**
	 * 获取充值信息
	 * @param int $recharge_id
	 * @return array
	 */
	public function get_recharge_info($recharge_id)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_recharge_info($recharge_id);
	}
	
	/**
	 * 获取充值信息，根据报名ID
	 * 若没使用第三方支付，则获取不到
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
	 * 提交支付
	 * @param string $channel_module 渠道模块，充值recharge
	 * @param string $channel_rid 渠道关联ID，充值ID
	 * @param array $payment_info
	 * @return array
	 * @tutorial
	 * $payment_info = array(
	 * 'third_code' => '',	//第三方支付方式
	 * 'subject' => '',	//商品名称
	 * 'body' => '',	//商品描述
	 * 'amount' => '',	//支付金额
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
	 * 提交充值记录，准备去支付
	 * @param string $recharge_type 充值类型 recharge consume bail activity date activity_pc task_request task_coin mall_order
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
	 * 	'channel_return' => '',	//支付成功，同步跳转页面
	 * 	'channel_notify' => '',	//支付成功，异步通知页面
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
			$result['message'] = 'recharge_type错误';
			$result['request_data'] = '';
			return $result;
		}
		
		if( $user_id<1 )
		{
			$result['error'] = 12;
			$result['message'] = 'user_id错误';
			$result['request_data'] = '';
			return $result;
		}
		
		if( $amount<=0 )
		{
			$result['error'] = 13;
			$result['message'] = 'amount错误';
			$result['request_data'] = '';
			return $result;
		}
		
		if( strlen($third_code)<1 )
		{
			$result['error'] = 14;
			$result['message'] = 'third_code错误';
			$result['request_data'] = '';
			return $result;
		}
		
		if( in_array($recharge_type, array('activity', 'activity_pc', 'task_request', 'task_coin', 'mall_order')) )
		{
			if( $event_id<1 )
			{
				$result['error'] = 15;
				$result['message'] = 'event_id错误';
				$result['request_data'] = '';
				return $result;
			}
			
			if( strlen($enroll_id_str)<1 )
			{
				$result['error'] = 16;
				$result['message'] = 'enroll_id_str错误';
				$result['request_data'] = '';
				return $result;
			}
		}
		elseif( in_array($recharge_type, array('date')) )
		{
			if( $date_id<1 )
			{
				$result['error'] = 17;
				$result['message'] = 'date_id错误';
				$result['request_data'] = '';
				return $result;
			}
        }
        
        /*
        if( $third_code=='tenpay_wxapp' && !in_array($user_id, array(100003)) )
        {
        	$result['error'] = 18;
        	$result['message'] = '微信支付升级中，请先使用其它方式。';
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
			$subject = '钱包充值';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/recharge_notify_url');
		}
		elseif( $recharge_type=='consume' )
		{
			$subject = '在线充值';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/recharge_notify_url');
		}
		elseif( $recharge_type=='bail' )
		{
			$subject = '信用金充值';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/recharge_notify_url');
		}
		elseif( $recharge_type=='activity' )
		{
			$subject = '活动报名';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/activity_notify_url');
		}
		elseif( $recharge_type=='date' )
		{
			$subject = '约拍邀请';
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/date_notify_url');
		}
		elseif( $recharge_type=='activity_pc' )
		{
			$subject = '活动报名[PC]';
			if( empty($channel_return) ) $channel_return = POCO_APP_PAI::ini('payment/activity_pc_return_url');
			if( empty($channel_notify) ) $channel_notify = POCO_APP_PAI::ini('payment/activity_pc_notify_url');
		}
		elseif( $recharge_type=='task_request' )
		{
			$subject = '服务金支付';
		}
		elseif( $recharge_type=='task_coin' )
		{
			$subject = '生意卡购买';
		}
		elseif( $recharge_type=='mall_order' )
		{
			$subject = '订单支付';
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
			$result['message'] = 'recharge_id错误';
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
			'subject' => '约约-'.$subject,
			'amount' => $amount,
			'channel_return' => $channel_return,
			'channel_notify' => $channel_notify,
			'openid' 		 => $openid
		);
		
		$payment_obj = POCO::singleton('ecpay_payment_class');
		return $payment_obj->submit_payment($this->channel_code, $this->channel_module, $recharge_id, $payment_info);
	}
	
	/**
	 * 手动充值
	 * @param string $recharge_type 充值类型 recharge bail activity date activity_pc
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
	 * 	'third_code' => '', //支付方式
	 * 	'third_oid' => '', //流水号
	 *  'real_name' => '',	//买家真实姓名
	 *  'third_buyer' => '',//买家账号
	 *  'third_seller' => '',//卖家账号
	 * 	'ref_id' => 0, //关联ID，OA订单的ID
	 * 	'receive_time' => 0, //收款时间
	 * 	'remark' => '', //收款备注
	 *  'subject' => '', //商品名称
	 *  'body' => '', //商品描述
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
			$result['message'] = '参数错误';
			return $result;
		}
		
		if( in_array($recharge_type, array('activity', 'activity_pc', 'mall_order')) )
		{
			if( $event_id<1 || strlen($enroll_id_str)<1 )
			{
				$result['error'] = 11;
				$result['message'] = '参数错误';
				return $result;
			}
		}
		elseif( in_array($recharge_type, array('date')) )
		{
			if( $date_id<1 )
			{
				$result['error'] = 12;
				$result['message'] = '参数错误';
				return $result;
			}
		}
		
		$subject = '';
		if( $recharge_type=='recharge' )
		{
			$subject = '钱包充值';
		}
		if( $recharge_type=='consume' )
		{
			$subject = '在线充值';
		}
		elseif( $recharge_type=='bail' )
		{
			$subject = '信用金充值';
		}
		elseif( $recharge_type=='activity' )
		{
			$subject = '活动报名';
		}
		elseif( $recharge_type=='date' )
		{
			$subject = '约拍邀请';
		}
		elseif( $recharge_type=='activity_pc' )
		{
			$subject = '活动报名[PC]';
		}
		elseif( $recharge_type=='task_request' )
		{
			$subject = '服务金支付';
		}
		elseif( $recharge_type=='task_coin' )
		{
			$subject = '生意卡购买';
		}
		elseif( $recharge_type=='mall_order' )
		{
			$subject = '订单支付';
		}
		
		$more_info = array(
			'third_code' => $third_code, //支付方式，必填
			'third_oid' => $third_oid, //流水号，必填
			'real_name' => $real_name,	//买家真实姓名
			'third_buyer' => $third_buyer,//买家账号
			'third_seller' => $third_seller,//卖家账号
			'ref_id' => $ref_id, //关联ID，OA订单的ID
			'receive_time' => $receive_time, //收款时间
			'remark' => $remark, //收款备注
			'subject' => $subject, //商品名称
			'body' => '', //商品描述
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
		$result['message'] = '成功';
		$result['recharge_id'] = $recharge_rst['recharge_id'];
		$result['payment_no'] = $recharge_rst['payment_no'];
		return $result;
	}
	
	/**
	 * 手动退款，提交退款申请
	 * @param string $repay_type 退款类型 recharge
	 * @param int $user_id 用户ID
	 * @param double $amount 退款金额
	 * @param array $more_info
	 * @return array
	 * @tutorial
	 * array(
	 * 'recharge_id' => 0,	//充值ID
	 * 'payment_no' => '', //充值的支付号
	 * 'third_code' => '', //支付方式 manual
	 * 'third_oid' => '',	//支付流水号
	 * 'real_name' => '',	//真实姓名
	 * 'third_buyer' => '',	//账号
	 * 'remark' => '', //备注
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
		
		//检查参数
		if( !in_array($repay_type, array('recharge')) || $user_id<1 || $amount<=0 || $recharge_id<1 || strlen($third_code)<1 )
		{
			$result['error'] = 10;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$subject = '';
		if( $repay_type=='recharge' )
		{
			$subject = '钱包充值';
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
			$result['message'] = '处理失败';
			return $result;
		}
		
		//日志 http://yp.yueus.com/logs/201502/03_pai_payment.txt
		pai_log_class::add_log(array('repay_id'=>$repay_id), 'manual_repay', 'pai_payment');
		
		$result['error'] = 0;
		$result['message'] = '成功';
		$result['repay_id'] = $repay_id;
		return $result;
	}
	
	/**
	 * 对报名进行支付
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
			$result['message'] = '约拍ID或报名ID错误';
			return $result;
		}
		$enroll_obj  		= POCO::singleton('event_enroll_class');
		$pai_recharge_obj   = POCO::singleton('ecpay_pai_recharge_class');
		$enroll_info 		= get_enroll_by_enroll_id($enroll_id);
		if( empty( $enroll_info ) ){

			$result['error']   = 20;
			$result['message'] = '找不到enroll_id对应的数据';
			return $result;

		}
		$search_arr 	= array( 'date_id'=>$date_id,'status'=>1 );
		$recharge_info  = $pai_recharge_obj->get_recharge_info_by_search($search_arr);
		
		//优惠券
		if($enroll_info['is_use_coupon'])
		{
			$total_cost = $enroll_info['original_price']*1;
			$discount_price = $enroll_info['discount_price']*1;
			$pending_cost = $total_cost - $discount_price;
			if( $pending_cost<=0 )
			{
				$result['error']   = 20;
				$result['message'] = '待付金额错误';
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
			//不存在充值  则完全为余额支付
			$is_balance = 1;
			$is_third   = 0; 

		}
		else{
			if($pending_cost>$recharge_info['amount']){
				//支付金额大于充值金额  则为余额支付和第三方支付各付一部分
				$is_balance = 1;
				$is_third   = 1; 

			}
			else{
				//否则第三方完全支付
				$is_balance = 0;
				$is_third   = 1; 
			}
		}
		$available_balance  = $this->get_user_available_balance($enroll_info['user_id']);
		if( bccomp($available_balance, $pending_cost, 2)==-1 )
		{
			$result['error'] = 20;
			$result['message'] = '账户可用余额不足';
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
		$result['message'] = '成功';
		return $result;
	}
	
	/**
	 * 对报名进行支付
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
			$result['message'] = '非法的payment_no';
			return $result;

		}
		//继续执行活动报名
		$channel_param = trim($payment_info['channel_param']);
		if( strlen($channel_param)<1 )
		{
			$result['error']   = 20;
			$result['message'] = 'channel_param格式错误';
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
			$result['message'] = '活动ID或报名ID错误';
			return $result;
		}
		$enroll_obj  		= POCO::singleton('event_enroll_class');
		$enroll_cost_detail = $enroll_obj->get_enroll_cost_by_arr( $enroll_id_arr,0 );
		if( empty($enroll_cost_detail) )
		{
			$result['error'] 	= 0;
			$result['message']  = '没有需要付款的报名';
			return $result;
		}
		$list 		 		= $enroll_cost_detail['list'];
		$total_cost  		= $enroll_cost_detail['total_cost'];
		$total_discount  	= $enroll_cost_detail['total_discount']; //计算优惠金额
		$user_id     		= get_relate_yue_id($list[0]['user_id']);

		$total_pending      = bcsub($total_cost, $total_discount, 2);
		if( $total_pending<=0 )
		{
			$result['error'] 	= 0;
			$result['message']  = '没有需要付款的报名';
			return $result;
		}
		
		if( empty($list) )
		{
			$result['error'] = 0;
			$result['message'] = '没有需要付款的报名';
			return $result;
		}
		$available_balance = $this->get_user_available_balance($user_id);
		if( bccomp($available_balance, $total_pending, 2)==-1 )
		{
			$result['error'] = 40;
			$result['message'] = '账户可用余额不足';
			return $result;
		}
		if( $total_pending > $payment_info['amount'] ){
			//如果第三方支付总额大于支付宝支付成功的总额  说明不是全额支付 而是一部分余额，一部分第三方支付
			$is_balance = 1;
			$is_third   = 1; 

		}
		else{
			//否则第三方完全支付
			$is_balance = 0;
			$is_third   = 1; 

		}
		$recharge_id    = $payment_info['channel_rid'];
		//余额足够，直接产生交易
		$rel_data   = array('event_id'=>$event_id,'user_id'=>$user_id,'is_balance'=>$is_balance,'is_third'=>$is_third,'recharge_id'=>$recharge_id);
		$submit_ret = $this->batch_submit_trade_out( $list,$rel_data );
		if( $submit_ret > 0 ){
		
			$result['error']   	   = 0;
			$result['trade_succ']  = $submit_ret;
			$result['message'] 	   = '成功';
			return $result;
		
		}
		else{

			$result['error']   		= 50;
			$result['message'] 		= '插入交易表失败';
			return $result;

		}

	}

	/**
	 * 对免费活动进行支付或app免费活动及余额足够的情况下支付
	 * @param int 	 $event_id
	 * @param string $enroll_id_arr 报名ID数组
	 * @return boolean
	 */
	public function pay_enroll_by_balance($event_id, $enroll_id_arr){

		if( $event_id<1 ){

		 	trace("非法参数 event_id",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;	

		}
		if( empty($enroll_id_arr) || !is_array($enroll_id_arr)){

 			trace("非法参数 enroll_id_arr",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
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
			$result['message'] = '参与ID非法';
			return $result;
		}
		$rel_data   = array('event_id'=>$event_id,'user_id'=>$user_id,'is_balance'=>$is_balance,'is_third'=>$is_third,'recharge_id'=>$recharge_id);
		$submit_ret = $this->batch_submit_trade_out( $list,$rel_data );
		if( $submit_ret > 0 ){
		
			$result['error']   	   = 0;
			$result['trade_succ']  = $submit_ret;
			$result['message'] 	   = '成功';
			return $result;
		
		}
		else{

			$result['error']   		= 50;
			$result['message'] 		= '插入交易表失败';
			return $result;

		}

	}

	/**
	 *  通过报名数据数组批量提交交易
	 *  
	 */
	public function batch_submit_trade_out( $list,$rel_data )
	{
		$enroll_obj  = POCO::singleton('event_enroll_class');
		if( empty($list) || !is_array($list)){

 			trace("非法参数 list",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;

		}
		if( empty($rel_data) ){

			trace("非法参数 rel_data",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;

		}
		foreach($list as $k=>$v )
		{
			//传入优惠金额
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
				//修改报名状态
				$ret = $enroll_obj->update_enroll_pay_status( $v['enroll_id'], 1 );
				$ret && $affect_rows++;

			}

		}
		return $affect_rows;

	}
	
	/**
	 * 提交交易，并冻结（摄影师）
	 * @param int $event_id 活动ID
	 * @param int $enroll_id 报名ID
	 * @param int $user_id 用户ID
	 * @param double $total_amount 四舍五入，保留2位小数。总金额
	 * @param double $discount_amount 四舍五入，保留2位小数。优惠金额
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
	 * 提交支出交易（=>待支付=>已冻结）
	 * @param string $channel_module yuepai约拍  waipai外拍  task_request需求  task_coin生意卡
	 * @param int $event_id 活动ID、需求ID
	 * @param int $enroll_id 报名ID、报价ID
	 * @param int $user_id 用户ID
	 * @param double $total_amount 四舍五入，保留2位小数。总金额
	 * @param double $discount_amount 四舍五入，保留2位小数。优惠金额
	 * @param array $more_info array('org_user_id'=>0, 'is_balance'=>0, 'is_third'=>0, 'recharge_id'=>0, 'balance_appoint_str'=>'', 'subject'=>'', 'remark'=>'')
	 * @return array
	 * @tutorial
	 *
	 * balance_appoint_str 指定余额账户，默认所有，多个值时以竖线分隔。consume消费金  purse钱包余额
	 *
	 */
	public function submit_trade_out_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $discount_amount, $more_info=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->submit_trade_out($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $discount_amount, $more_info);
	}
	
	/**
	 * 提交交易（模特，组织者）
	 * @param int $event_id 活动ID
	 * @param int $enroll_id 报名ID，模特，组织者
	 * @param int $user_id 用户ID
	 * @param double $total_amount 四舍五入，保留2位小数。总金额
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
		
		//计算分成金额（服务费）
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
	 * 提交收入交易（=>待支付）
	 * @param string $channel_module
	 * @param int $event_id 活动ID
	 * @param int $enroll_id 报名ID，模特，组织者
	 * @param int $user_id 用户ID
	 * @param double $total_amount 四舍五入，保留2位小数。总金额
	 * @param array $more_info array('discount_amount'=>0.00, 'service_amount'=>0.00, 'org_user_id'=>0, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>'')
	 * @return array
	 */
	public function submit_trade_in_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->submit_trade_in($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info);
	}
	
	/**
	 * 提交交易（部分退款，退还给摄影师）
	 * @param int $event_id 活动ID
	 * @param int $enroll_id 报名ID，模特，组织者
	 * @param int $user_id 用户ID
	 * @param double $total_amount 四舍五入，保留2位小数。总金额
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
	 * 提交退款交易（=>待支付）
	 * @param string $channel_module
	 * @param int $event_id 活动ID、需求ID
	 * @param int $enroll_id 报名ID、报价ID，模特，组织者
	 * @param int $user_id 用户ID
	 * @param double $total_amount 四舍五入，保留2位小数。总金额
	 * @param array $more_info array('org_user_id'=>0, 'is_balance'=>0, 'is_third'=>0, 'recharge_id'=>0, 'subject'=>'', 'remark'=>'')
	 * @return array
	 */
	public function submit_trade_refund_v2($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->submit_trade_refund($channel_module, $event_id, $enroll_id, $user_id, $total_amount, $more_info);
	}
	
	/**
	 * 获取交易信息，根据报名ID
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
	 * 冻结交易
	 * 支出者（摄影师）：已冻结，支出者账户增加应付金额
	 * 收入者（模特、组织者）：已冻结，活动账户增加应付金额
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
	 * 支付成功的回调
	 * 检查支付状态，检查充值状态
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
	 * 支付成功的回调
	 * 检查支付状态，检查充值状态，执行充值
	 * @param array $payment_info
	 * @return array
	 */
	public function notify_recharge($payment_info)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->approve_recharge_by_payment_info($payment_info);
	}
	
	/**
	 * 关闭交易，取消交易
	 * 待支付：已关闭
	 * 支出者已冻结：已关闭，支出者账户减少应付金额
	 * 收入者已冻结：已关闭，活动账户减少应付金额
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
	 * 关闭交易，取消交易
	 * 待支付：已关闭
	 * 支出者已冻结：已关闭，支出者账户减少应付金额
	 * 收入者已冻结：已关闭，活动账户减少应付金额
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
	 * 活动确认
	 * 摄影师，交易已支付，摄影师余额转到活动余额
	 * 组织者，交易已冻结，增加活动应付金额
	 * 确认前，需要将候补的报名取消，保证活动收支平衡
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
	 * 取消活动
	 * 先处理收入者，将钱解冻或退回活动账户
	 * 再处理支出者，将钱解冻或退回支出者账户
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
	 * 取消活动
	 * 先处理收入者，将钱解冻或退回活动账户
	 * 再处理支出者，将钱解冻或退回支出者账户
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
	 * 活动结束
	 * 先处理支出者，仅检查交易状态
	 * 再处理收入者，将钱转给收入者
	 * @param int $event_id
	 * @return array
	 */
	private function finish_event($event_id)
	{
		$channel_module = $this->get_channel_module_by_event_id($event_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		$ret = $trade_obj->finish_event($channel_module, $event_id);
		
		//调用积分系统，不允许重复调用
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
	 * 结束活动（完成活动）
	 * @param int $event_id 活动ID
	 * @param array $refund_list 退款列表  array( array('user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $in_list 收入列表  array( array('discount_amount'=>0.00, 'user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $coupon_refund_list 优惠券退款列表 array( array('id'=>0), )
	 * @param array $coupon_cash_list 优惠兑现列表 array( array('id'=>0, 'user_id'=>0, 'org_user_id'=>0, 'amount'=>0.00, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>''), )
	 * @return array
	 */
	public function end_event($event_id, $refund_list, $in_list, $coupon_refund_list=array(), $coupon_cash_list=array())
	{
		$channel_module = $this->get_channel_module_by_event_id($event_id);
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		$ret = $trade_obj->end_event($channel_module, $event_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
		
		//调用积分系统，不允许重复调用
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
	 * 结束活动（完成活动）
	 * @param string $channel_module
	 * @param int $event_id 活动ID
	 * @param array $refund_list 退款列表  array( array('user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $in_list 收入列表  array( array('discount_amount'=>0.00, 'user_id'=>0, 'org_user_id'=>0, 'apply_id'=>0, 'amount'=>0, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>'' ), )
	 * @param array $coupon_refund_list 优惠券退款列表 array( array('id'=>0), )
	 * @param array $coupon_cash_list 优惠兑现列表 array( array('id'=>0, 'user_id'=>0, 'org_user_id'=>0, 'amount'=>0.00, 'org_amount'=>0.00, 'subject'=>'', 'remark'=>''), )
	 * @return array
	 */
	public function end_event_v2($channel_module, $event_id, $refund_list, $in_list, $coupon_refund_list=array(), $coupon_cash_list=array())
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->end_event($channel_module, $event_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
	}
	
	/**
	 * 获取最近24小时的消费金额，用于判断是否需要出手机验证码
	 * @param int $user_id
	 * @return double
	 */
	private function get_trade_amount_by_user_id($user_id)
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_trade_amount_by_user_id($user_id);
	}
	
	/**
	 * 获取机构的未结算金额
	 * @param int $org_user_id
	 * @param string $channel_module yuepai约拍 waipai外拍
	 * @return double
	 */
	public function get_unsettle_org_amount($org_user_id, $channel_module='')
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_unsettle_org_amount($org_user_id, $channel_module);
	}
	
	/**
	 * 获取机构的已结算金额
	 * @param int $settle_id
	 * @param string $channel_module yuepai约拍 waipai外拍
	 * @return double
	 */
	public function get_settle_org_amount($settle_id, $channel_module='')
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_org_amount($settle_id, $channel_module);
	}
	
	/**
	 * 获取机构的已结算金额
	 * @param int $settle_id
	 * @param string $channel_module yuepai约拍 waipai外拍
	 * @return array
	 */
	public function get_settle_org_amount_info($settle_id, $channel_module='')
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_org_amount_info($settle_id, $channel_module);
	}
	
	/**
	 * 获取机构的未结算列表
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
	 * 获取机构的未结算活动ID
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
	 * 获取机构结算信息
	 * @param int $settle_id
	 * @return array
	 */
	public function get_settle_info($settle_id)
	{
		$pai_org_obj = POCO::singleton('ecpay_pai_org_class');
		return $pai_org_obj->get_settle_info($settle_id);
	}
	
	/**
	 * 获取机构的已结算列表
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
	 * 获取机构的已结算的关联列表
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
	 * 冻结用户的余额，理由是邀请
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
			//不存在充值  则完全为余额支付
			$is_balance = 1;
			$is_third   = 0; 
			$subject = '约拍邀请';
		}
		else{
			if($amount>$recharge_info['amount']){
				//支付金额大于充值金额  则为余额支付和第三方支付各付一部分
				$is_balance = 1;
				$is_third   = 1; 

			}
			else{
				//否则第三方完全支付
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
	 * 解冻用户的余额，理由是邀请
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
	 * 模特拒绝邀请
	 * @param int $date_id
	 * @return boolean
	 */
	public function refused_date($date_id){

		$date_obj = POCO::singleton('ecpay_pai_date_class');
		return $date_obj->refused_date($date_id);

	}

	/**
	 * 模特接受邀请
	 * @param int $date_id
	 * @return boolean
	 */
	public function accepted_date($date_id){

		$date_obj = POCO::singleton('ecpay_pai_date_class');
		return $date_obj->accepted_date($date_id);

	}

	/**
	 * 申请提现
	 * @param string $withdraw_type 钱包提现 withdraw  信用金提现 bail
	 * @param int $user_id
	 * @param double $amount
	 * @param string $real_name
	 * @param string $third_type alipay支付宝 tenpay财付通
	 * @param string $third_account
	 * @return int -1为账户无可提现的余额 -2为用户余额不足 0为参数错 正常返回为提现ID
	 */
	public function submit_withdraw($withdraw_type,$user_id,$amount,$real_name,$third_type,$third_account)
	{
		//禁止机构模特、机构商家提现
		$pai_model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');
		$pai_model_relate_org_ret = $pai_model_relate_org_obj->get_org_info_by_user_id($user_id);
		if( $pai_model_relate_org_ret )
		{
			return 0;
		}
		
		if( $withdraw_type == 'withdraw' )
		{
			$subject = '钱包提现';
		}
		elseif( $withdraw_type == 'bail' )
		{
			$subject = '信用金提现';
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
	 * 获取交易列表
	 * @param string $search_arr   查询条件数组 array('user_id'=>'','event_id'=>,'status'=>'','is_carryover'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
	 * @param string $limit        查询条数
	 * @param string $order_by     排序条件
	 * @return array|int
	 */
	public function get_trade_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'trade_id DESC')
	{
		$trade_obj = POCO::singleton('ecpay_pai_trade_v2_class');
		return $trade_obj->get_trade_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * 获取充值列表
	 * @param string $search_arr   查询条件数组 array('user_id'=>'','recharge_type'=>'','third_code'=>,'status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
	 * @param string $limit        查询条数
	 * @param string $order_by     排序条件
	 * @return array|int
	 */
	public function get_recharge_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'recharge_id DESC')
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * 获取提现列表
	 * @param string $search_arr   查询条件数组 array('user_id'=>'','status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
	 * @param string $limit        查询条数
	 * @param string $order_by     排序条件
	 * @return array|int
	 */
	public function get_withdraw_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'withdraw_id DESC')
	{
		$withdraw_obj = POCO::singleton('ecpay_pai_withdraw_class');
		return $withdraw_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * 获取转账列表
	 *
	 * @param string $search_arr    查询条件数组 array('user_id'=>'','status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
	 * @param string $limit        查询条数
	 * @param string $order_by     排序条件
	 * @return array|int
	 */
	public function get_transfer_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'transfer_id DESC')
	{
		$transfer_obj = POCO::singleton('ecpay_pai_transfer_class');
		return $transfer_obj->get_transfer_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * 获取退回列表
	 * @param string $search_arr    查询条件数组 array('user_id'=>'','status'=>'','min_add_time'=>'','max_add_time'=>'')
	 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
	 * @param string $limit        查询条数
	 * @param string $order_by     排序条件
	 * @return array|int
	 */
	public function get_repay_list_by_search($search_arr, $b_select_count = false, $limit = '', $order_by = 'repay_id DESC')
	{
		$repay_obj = POCO::singleton('ecpay_pai_repay_class');
		return $repay_obj->get_list_by_search($search_arr, $b_select_count, $limit, $order_by);
	}
	
	/**
	 * 获取账单交易列表
	 * @param int $user_id
	 * @param string $b_select_count
	 * @param string $limit
	 * @return array array(
	 * 		array(
	 * 			'subject' => '', //名称
	 * 			'is_invalid' => 0, //是否无效，0有效，1无效
	 * 			'flow_type' => 0, //符号，0正数，1负数
	 * 			'amount' => 0, //金额
	 * 			'status' => '', //状态
	 * 			'remark' => '', //备注
	 * 			'repay_str_arr' => array(), //退款
	 * 			'add_date' => '', //日期
	 * 		)
	 * )
	 */
	public function get_bill_trade_list($user_id, $b_select_count=false, $limit='0,10', $order_by='trade_id DESC')
	{
		$bill_list= array();
		
		//检查参数
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $bill_list;
		}
		
		//查询
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
		
		//整理数据
		$trade_type_arr = array(
			'in' => '收入',
			'out' => '支出',
			'refund' => '退款',
			'transfer' => '转账',
		);
		$status_arr = array(
			0 => '等待支付',
			7 => '交易关闭',
			8 => '交易成功',
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
			
			//名称
			$trade_type_name = trim($trade_type_arr[$trade_type]);
			if( strlen($trade_type_name)<1 ) $trade_type_name = '未知';
			if( strlen($subject)>0 )
			{
				$subject = $trade_type_name . '-' . $subject;
			}
			else
			{
				$subject = $trade_type_name;
			}
			
			//状态
			$status_str = trim($status_arr[$status]);
			if( strlen($status_str)<1 ) $status_str = '处理中';
			
			//计算金额
			$repay_str_arr = array();
			$amount = 0;
			$remark = ''; //备注
			if( $trade_type=='in' ) //收入
			{
				$coupon_event_amount = $pai_coupon_obj->sum_ref_event_cash_amount_by_event_id($channel_module, $event_id); //总补贴金额
				$coupon_in_amount = $pai_coupon_obj->sum_ref_order_in_amount_by_in_user_id($channel_module, $event_id, $user_id); //收入者补贴金额
				$amount = $pending_amount + $coupon_in_amount;
				$none_amount = $discount_amount - $coupon_event_amount; //让利金额
				if( $none_amount>0 )
				{
					$none_amount = number_format($none_amount, 2, '.', '');
					$remark = "账单金额：{$total_amount}，优惠金额支出：-{$none_amount}";
				}
			}
			elseif( $trade_type=='out' ) //支出
			{
				if( $status==7 ) //关闭
				{
					$repay_str_arr = $this->get_bill_repay_str_arr($user_id, $trade_id, $pending_amount);
				}
				$amount = $pending_amount;
				if( $discount_amount>0 )
				{
					$remark = "账单金额：{$total_amount}，优惠金额：{$discount_amount}";
				}
			}
			elseif( $trade_type=='refund' ) //退款
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
			
			//金额的符号，0正数，1负数
			$flow_type_tmp = ($flow_type=='in') ? 0 : 1;
			
			//是否无效
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
	 * 获取账单退款信息
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
				$status_name = '正在退回';
			}
			elseif( $status==1 )
			{
				$status_name = '已退回';
			}
			
			$third_code_name = '';
			if( in_array($third_code, array('tenpay_wxpub', 'tenpay_wxapp')) )
			{
				$third_code_name = '微信';
			}
			elseif( in_array($third_code, array('alipay', 'alipay_wap', 'alipay_purse')) )
			{
				$third_code_name = '支付宝';
			}
			
			$remain_amount = $remain_amount - $amount;
			
			$repay_str_arr[] = "{$status_name}[{$third_code_name}] {$amount}元";
		}
		if( $remain_amount>0 )
		{
			$remain_amount = number_format($remain_amount, 2, '.', '');
			$repay_str_arr[] = "已退回[约约钱包] {$remain_amount}元";
		}
		
		return $repay_str_arr;
	}
	
	/**
	 * 获取账单充值列表
	 * @param int $user_id
	 * @param string $b_select_count
	 * @param string $limit
	 * @return array
	 */
	public function get_bill_recharge_list($user_id, $b_select_count=false, $limit='0,10', $order_by='recharge_id DESC')
	{
		$bill_list= array();
		
		//检查参数
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $bill_list;
		}
		
		//查询
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
		
		//整理数据
		$status_arr = array(
			0 => '等待支付',
			1 => '充值成功',
			2 => '充值失败',
		);
		foreach($recharge_list as $recharge_info)
		{
			$subject = trim($recharge_info['subject']);
			$amount = trim($recharge_info['amount']);
			$status = intval($recharge_info['status']);
			$add_time = intval($recharge_info['add_time']);
			
			//名称
			$recharge_type_name = '充值';
			if( strlen($subject)>0 )
			{
				$subject = $recharge_type_name . '-' . $subject;
			}
			else
			{
				$subject = $recharge_type_name;
			}
			
			//状态
			$status_str = trim($status_arr[$status]);
			if( strlen($status_str)<1 ) $status_str = '处理中';
			
			//备注
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
	 * 获取账单提现列表
	 * @param int $user_id
	 * @param string $b_select_count
	 * @param string $limit
	 * @return array
	 */
	public function get_bill_withdraw_list($user_id, $b_select_count=false, $limit='0,10', $order_by='withdraw_id DESC')
	{
		$bill_list= array();
		
		//检查参数
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $bill_list;
		}
		
		//查询
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
		
		//整理数据
		$status_arr = array(
			1 => '提现成功',
			2 => '提现失败',
		);
		foreach($withdraw_list as $withdraw_info)
		{
			$subject = trim($withdraw_info['subject']);
			$amount = trim($withdraw_info['amount']);
			$status = intval($withdraw_info['status']);
			$add_time = intval($withdraw_info['add_time']);
			
			//名称
			$recharge_type_name = '提现';
			if( strlen($subject)>0 )
			{
				$subject = $recharge_type_name . '-' . $subject;
			}
			else
			{
				$subject = $recharge_type_name;
			}
			
			//状态
			$status_str = trim($status_arr[$status]);
			if( strlen($status_str)<1 ) $status_str = '处理中';
			
			//备注
			$remark = '';
			
			//是否无效
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
	 * 检查用户是否使用过支付系统，
	 * 用于是否允许转换用户角色
	 * @param int $user_id
	 * @return bool true使用过，false未使用
	 */
	public function check_user_used($user_id)
	{
		$user_id = intval($user_id);
		if ( empty( $user_id ) ) 
        {
            trace("非法参数 user_id 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
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
	 * 获取最小支付金额
	 * @return boolean
	 */
	public function get_min_pay_amount(){

		return  $this->min_pay_amount;

	}

	/**
	 * 获取最大支付金额
	 * @return boolean
	 */
	public function get_max_pay_amount(){

		return  $this->max_pay_amount;

	}
	
	/**
	 * 获取最小支付金额，商家提现
	 * @return double
	 */
	public function get_min_seller_withdraw_amount()
	{
		return 10;
	}
	
	/**
	 * 获取最大支付金额，商家提现
	 * @return double
	 */
	public function get_max_seller_withdraw_amount()
	{
		return 100000;
	}
	
	/**
	 * 获取充值卡商家数据
	 * @param int $user_id
	 * @return array
	 */
	public function get_card_seller_info($user_id)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_card_seller_info($user_id);
	}
	
	/**
	 * 获取是否充值卡商家
	 * @param int $user_id
	 * @return bool
	 */
	public function check_is_card_seller($user_id)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->check_is_card_seller($user_id);
	}
	
	/**
	 * 获取充值卡信息
	 * @param string $card_no
	 * @return array
	 */
	public function get_card_info($card_no)
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_card_info($card_no);
	}
	
	/**
	 * 获取充值卡列表
	 * @param int $user_id
	 * @param int $keyword 卡号关键字
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit
	 * @param string $fields 查询字段
	 * @return array|int
	 */
	public function get_card_list_by_seller($user_id, $keyword, $b_select_count=false, $where_str='', $order_by='card_id ASC', $limit='0,20', $fields='*')
	{
		$recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
		return $recharge_obj->get_card_list_by_seller($user_id, $keyword, $b_select_count, $where_str, $order_by, $limit, $fields);
	}
	
	/**
	 * 激活充值卡
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
	 * 作废充值卡
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
	 * 使用充值卡充值
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
	 * 取的日统计数据和业务系统对账
	 * @param int $request_time 请求时间
	 * @return array 不成功时返回空数组
	 */
	public function collect_daily_sell_stats_report($request_time)
	{
		$report_obj = POCO::singleton('ecpay_pai_report_class');
		return $report_obj->collect_daily_sell_stats_report($request_time);
	}
	
}
