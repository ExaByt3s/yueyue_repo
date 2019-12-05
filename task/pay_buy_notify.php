<?php
/** 
 * 购买生意卡服务器异步回调
 * 
 * @author Henry
 * @copyright 2015-04-16
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$payment_obj = POCO::singleton('pai_payment_class');

//获取支付信息
$payment_no = trim($_INPUT['payment_no']);
$payment_info = $payment_obj->get_payment_info($payment_no);

//文本日志 http://yp.yueus.com/logs/201504/01_coin.txt
pai_log_class::add_log($payment_info, 'pay_buy_notify::begin', 'coin');

if( empty($payment_info) )
{
    echo 'payment_no error';
    exit();
}

$channel_module = trim($payment_info['channel_module']);
if( $channel_module!='coin_buy' )
{
	echo 'channel_module error';
	exit();
}

$payment_status = intval($payment_info['status']);
if( $payment_status!=8 )
{
	echo 'status error';
	exit();
}

//购买生意卡
$task_coin_obj = POCO::singleton('pai_task_coin_class');
$pay_ret = $task_coin_obj->pay_buy_by_payment_info($payment_info);
if( $pay_ret['result']!=1 )
{
	echo 'pay_quotes error ' . var_export($pay_ret, true);
	exit();
}

//继续报价
$buy_id = intval($payment_info['channel_rid']); //购买ID
$buy_info = $task_coin_obj->get_buy_info($buy_id);
$quotes_id = intval($buy_info['quotes_id']);
if( $quotes_id>0 )
{
	$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
	$ret = $task_quotes_obj->pay_quotes_coins($quotes_id);
	//文本日志 http://yp.yueus.com/logs/201504/01_coin.txt
	pai_log_class::add_log($ret, 'pay_buy_notify::pay_quotes_coins', 'coin');
}

echo 'success';
