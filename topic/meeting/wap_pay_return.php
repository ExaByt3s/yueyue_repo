<?php
/**
 * 支付成功，网页跳转同步通知
 * 注意：一般不在同步通知这里更新支付状态
 * 作为demo参考使用
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$payment_no = trim($_INPUT['payment_no']); //支付号

//获取支付信息
$payment_obj = POCO::singleton('pai_payment_class');
$payment_info = $payment_obj->get_payment_info($payment_no);
if( empty($payment_info) )
{
    echo "支付号有误";
    exit();
}

$payment_status = intval($payment_info['status']);
if( !in_array($payment_status, array(1, 8)) )
{
    echo "支付未成功";
    exit();
}

$channel_rid = intval($payment_info['channel_rid']); //报名ID、订单号
$third_total_fee = $payment_info['third_total_fee']*1; //实收金额

echo "支付已成功 {$channel_rid} {$third_total_fee}";
exit();
