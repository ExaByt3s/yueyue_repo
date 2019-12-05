<?php
/** 
 * 商城订单服务器回调
 * 
 * @author xiaowu
 * @copyright 2015-06-17
 */

include_once('../common.inc.php');
$payment_obj = POCO::singleton('pai_payment_class');

//获取支付信息
$payment_no = trim($_INPUT['payment_no']);
$payment_info = $payment_obj->get_payment_info($payment_no);

//文本日志 http://yp.yueus.com/logs/201504/01_quotes.txt
pai_log_class::add_log($payment_info, 'pay_order_notify', 'mall_order');

if( empty($payment_info) )
{
    echo 'payment_no error';
    exit();
}

$channel_module = trim($payment_info['channel_module']);
if( $channel_module=='recharge' )
{
    //检查支付状态，检查充值状态，执行充值
    $result = $payment_obj->notify_recharge($payment_info);
    if( $result['error']!==0 && $result['error']!==1 )
    {
        print_r($result);
        exit();
    }
    
    $mall_order_obj = POCO::singleton('pai_mall_order_class');
    $pay_ret = $mall_order_obj->pay_order_by_payment_info($payment_info);
    if( $pay_ret['result']!=1 )
    {
        echo 'pay_order_by_payment_info error ' . var_export($pay_ret, true);
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
