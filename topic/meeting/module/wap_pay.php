<?php
/**
 * WAP版去支付
 * 作为demo参考使用
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$channel_rid = 1; //模块关联ID：报名ID、订单号
$subject = '4月峰会'; //商品名称、活动名称
$amount = 0.01; //支付金额
$channel_return = 'http://yp.yueus.com/topic/meeting/wap_pay_return.php'; //支付成功，页面跳转返回
$channel_notify = 'http://yp.yueus.com/topic/meeting/pay_notify.php'; //服务器异步通知支付状态

$channel_module = 'meeting'; //模块类型：峰会
$third_code = 'alipay_wap'; //支付方式：支付宝WAP
$payment_info = array(
    'third_code' => $third_code,
    'subject' => '约约-' . $subject,
    'amount' => $amount,
    'channel_return' => $channel_return,
    'channel_notify' => $channel_notify,
);
$payment_obj = POCO::singleton('pai_payment_class');
$payment_ret = $payment_obj->submit_payment($channel_module, $channel_rid, $payment_info);
if( $payment_ret['error']===0 )
{
    $request_data = trim($payment_ret['request_data']);
    echo '<script>parent.location.href="'.$request_data.'"</script>';
}
else
{
    print_r($payment_ret);
}
