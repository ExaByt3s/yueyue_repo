<?php
/** 
 * 
 * 支付
 * 
 * 2015-4-11
 * 
 */
 
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

if( $yue_login_id<1 )
{
	die('no login');
}

$quotes_id = (int)$_INPUT['quotes_id'];
$num = (int)$_INPUT['num'];
if( $quotes_id<1 || $num<1 )
{
	die('params error');
}
if( $num!=20 )
{
	die('num error');
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
	die('submit_buy error');
}

$buy_id = intval($submit_ret['buy_id']);
$available_balance = 0;
$is_available_balance = 0;
$third_code = 'alipay';
$redirect_url = 'http://' . basename(dirname(__FILE__)) . '.yueus.com/quote_success.php';
$notify_url = 'http://' . basename(dirname(__FILE__)) . '.yueus.com/pay_buy_notify.php';

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
	die('submit_pay_buy error');
}
header("Location: " . $pay_ret['request_data']);
