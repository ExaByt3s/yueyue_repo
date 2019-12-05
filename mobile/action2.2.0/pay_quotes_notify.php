<?php
/** 
 * 报价服务器异步回调
 * 
 * @author Henry
 * @copyright 2015-04-14
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$payment_obj = POCO::singleton('pai_payment_class');

//获取支付信息
$payment_no = trim($_INPUT['payment_no']);
$payment_info = $payment_obj->get_payment_info($payment_no);

//文本日志 http://yp.yueus.com/logs/201504/01_quotes.txt
pai_log_class::add_log($payment_info, 'pay_quotes_notify', 'quotes');

if( empty($payment_info) )
{
    echo 'payment_no error';
    exit();
}

$channel_module = trim($payment_info['channel_module']);
if( $channel_module=='quotes' )
{
	$payment_status = intval($payment_info['status']);
	if( $payment_status!=8 )
	{
		echo 'status error';
		exit();
	}
	
	//支付定金
	$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
	$pay_ret = $task_quotes_obj->pay_quotes_by_payment_info($payment_info);
	if( $pay_ret['result']!=1 )
	{
		echo 'pay_quotes error ' . var_export($pay_ret, true);
		exit();
	}
	
	echo 'success';
	exit();
}
elseif( $channel_module=='recharge' )
{	
	//检查支付状态，检查充值状态，执行充值
	$result = $payment_obj->notify_recharge($payment_info);
	if( $result['error']!==0 && $result['error']!==1 )
	{
		print_r($result);
		exit();
	}
	
	$task_quotes_obj = POCO::singleton('pai_task_quotes_class');	
	$pay_ret = $task_quotes_obj->pay_quotes_by_payment_info_v2($payment_info);	
	if( $pay_ret['result']!=1 )
	{
		echo 'pay_quotes_by_payment_info_v2 error ' . var_export($pay_ret, true);
		exit();
	}
	
	echo 'success';	
	exit();
}
else
{
	echo 'channel_module error';
	exit();
}
