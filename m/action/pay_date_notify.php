<?php
/** 
 * 约拍服务器异步回调
 * 
 * @author Henry
 * @copyright 2014-06-23
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$payment_obj = POCO::singleton('pai_payment_class');

//获取支付信息
$payment_no = trim($_INPUT['payment_no']);
$payment_info = $payment_obj->get_payment_info($payment_no);
if( empty($payment_info) )
{
    echo 'payment_no error';
    exit();
}

//检查支付状态，检查充值状态，执行充值
$result = $payment_obj->notify_recharge($payment_info);
if( $result['error']!==0 && $result['error']!==1 )
{
    print_r($result);
    exit();
}


$channel_param = trim($payment_info['channel_param']);
if( strlen($channel_param)<1 )
{
    echo 'channel_param error';
    exit();
}

$channel_param_arr = unserialize($channel_param);
if( empty($channel_param_arr) )
{
    echo 'channel_param unserialize error';
    exit();
}
$date_id = intval($channel_param_arr['date_id']);
update_event_date_pay_status($date_id, $status = '1');

echo 'success';
