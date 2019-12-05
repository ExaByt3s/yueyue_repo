<?php
/** 
 * 
 * 报价
 * 
 * 2015-4-11
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$quotes_id = (int)$_INPUT['quotes_id'];
$num = (int)$_INPUT['num'];
if( $quotes_id<1 || $num<1 )
{
	$output_arr['code'] = -1;
	$output_arr['message'] = '参数错误';
	mobile_output($output_arr,false);
	exit();
}
if( $num!=20 )
{
	$output_arr['code'] = -2;
	$output_arr['message'] = '参数错误';
	mobile_output($output_arr,false);
	exit();
}

$subject = '生意卡购买';
$amount = 70;
$coins = 20;

$task_coin_obj = POCO::singleton('pai_task_coin_class');

/**
 * 提交购买
 * @param int $user_id
 * @param double $amount
 * @param double $coins
 * @param string $subject
 * @param int $quotes_id
 * @param array $more_info array('remark'=>'')
 * @return array array('result'=>0, 'message'=>'', 'buy_id'=>0)
 */
$submit_ret = $task_coin_obj->submit_buy($yue_login_id, $amount, $coins, $subject, $quotes_id);
if( $submit_ret['result']!=1 )
{
	$output_arr['code'] = -3;
	$output_arr['message'] = '提交错误';
	mobile_output($output_arr,false);
	exit();
}

$buy_id = intval($submit_ret['buy_id']);
$available_balance = 0;
$is_available_balance = 0;
$third_code = 'alipay_wap';
$redirect_url = 'http://www.yueus.com/task/m/success.php';
$notify_url = 'http://www.yueus.com/task/pay_buy_notify.php';

/**
 * 提交支付
 * @param int $buy_id
 * @param double $available_balance 页面当前余额
 * @param int $is_available_balance 是否使用余额，0否 1是
 * @param string $third_code 支付方式 alipay，当用户使用余额全额支付时可为空
 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
 * @param string $notify_url
 * @return array
 */
$pay_ret = $task_coin_obj->submit_pay_buy($buy_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url);
if( $pay_ret['result']!=1 )
{
	$output_arr['code'] = -4;
	$output_arr['message'] = '支付错误';
	mobile_output($output_arr,false);
	exit();
}

$output_arr['code'] = 1;
$output_arr['message'] = '成功';
$output_arr['request_data'] = $pay_ret['request_data'];

mobile_output($output_arr,false);
?>