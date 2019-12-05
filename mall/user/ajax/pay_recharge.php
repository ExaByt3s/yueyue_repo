<?php
/**
 * 信用金充值
 * @author Henry
 * @copyright 2014-09-13
 */

include_once('../common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}
$type = trim($_INPUT['type']);
$output_arr = array();

if( $type=='recharge' )	//去充值
{
	$payment_obj = POCO::singleton('pai_payment_class');
	$amount       = $_INPUT['amount']*1;
	$third_code   = trim($_INPUT['third_code']);
	$redirect_url = trim(urldecode($_INPUT['redirect_url']));
	$notify_url =  G_MALL_PROJECT_USER_ROOT . '/ajax/pay_recharge_notify.php';
	$more_info = array(
		'channel_return' => $redirect_url,
		'channel_notify' => $notify_url,
	);
	$recharge_ret = $payment_obj->submit_recharge('bail', $yue_login_id, $amount, $third_code, 0, '', 0, $more_info);
	if( $recharge_ret['error']!==0 )
	{
		$output_arr['code'] = 0;
		$output_arr['msg']  = $recharge_ret['message'];
		$output_arr['data'] = $recharge_ret['request_data'];
		$output_arr['payment_no'] = $recharge_ret['payment_no'];
		mall_mobile_output($output_arr, false);
		exit();
	}
	
	$output_arr['code'] = 1;
	$output_arr['msg']  = $recharge_ret['message'];
	$output_arr['data'] = $recharge_ret['request_data'];
	$output_arr['payment_no'] = $recharge_ret['payment_no'];
	$output_arr['channel_return'] = $redirect_url;
	$output_arr['third_code'] = $third_code;
	mall_mobile_output($output_arr, false);
	exit();
}
else
{
	$output_arr['code'] = 0;
	$output_arr['msg']  = 'type error';
	$output_arr['data'] = '';
	mall_mobile_output($output_arr, false);
	exit();
}
