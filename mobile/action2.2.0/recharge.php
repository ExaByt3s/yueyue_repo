<?php
/**
 * 信用金充值
 * @author Henry
 * @copyright 2014-09-13
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	$output_arr['code'] = 0;
	$output_arr['message']  = 'no login';
	$output_arr['data'] = '';
	mobile_output($output_arr, false);
	die();
}
$type = trim($_INPUT['type']);
$output_arr = array();

if( $type=='recharge' )	//去充值
{
	$payment_obj = POCO::singleton('pai_payment_class');
	$amount       = $_INPUT['amount']*1;
	$third_code   = trim($_INPUT['third_code']);
	$redirect_url = trim(urldecode($_INPUT['redirect_url']));
	$notify_url = G_PAI_APP_DOMAIN . '/mobile/' . basename(dirname(__FILE__)) . '/pay_recharge_notify.php';
	/*if( $amount < $payment_obj->get_min_pay_amount() || $amount>$payment_obj->get_max_pay_amount() ){

		$output_arr['code'] = 0;
		$output_arr['msg']  = '充值金额只能在10-10000之间';
		mobile_output($output_arr, false);
		exit();

	}*/
	$more_info = array(
		'channel_return' => $redirect_url,
		'channel_notify' => $notify_url,
	);
	$recharge_ret = $payment_obj->submit_recharge('consume', $yue_login_id, $amount, $third_code, 0, '', 0, $more_info);
	
	$channel_return = $redirect_url;
	if( !empty($recharge_ret['payment_no']) && strpos($channel_return, '#')!==false )
	{
		//兼容约拍的JS结构需求
		$channel_return .= '/';
		$channel_return .= "payment_no_{$recharge_ret['payment_no']}";
	}

	if( $recharge_ret['error']!==0 )
	{
		$output_arr['code'] = 0;
		$output_arr['message']  = $recharge_ret['message'];
		$output_arr['data'] = $recharge_ret['request_data'];
		$output_arr['payment_no'] = $recharge_ret['payment_no'];
		mobile_output($output_arr, false);
		exit();
	}

	$output_arr['code'] = 1;
	$output_arr['message']  = '支付功能成功调起';
	$output_arr['data'] = $recharge_ret['request_data'];
	$output_arr['payment_no'] = $recharge_ret['payment_no'];
	$output_arr['channel_return'] = $channel_return;
	$output_arr['third_code'] = $third_code;
	mobile_output($output_arr, false);
	exit();
}
else
{
	$output_arr['code'] = 0;
	$output_arr['message']  = 'type error';
	$output_arr['data'] = '';
	mobile_output($output_arr, false);
	exit();
}
