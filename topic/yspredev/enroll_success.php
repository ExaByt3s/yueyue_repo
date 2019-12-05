<?php
/**
 *
 * 约摄专题是否成功中转页处理
 *
 * author 星星
 *
 *
 * 2015-6-3
 *
 *
 */


/**
 * 支付成功，网页跳转同步通知
 * 注意：一般不在同步通知这里更新支付状态
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$type = trim($_INPUT['type']);
$enroll_res = trim($_INPUT['enroll_res']);

if($type=="normal")
{
    if(empty($enroll_res))
    {
        $res = "error";

    }
    else
    {
        $res = $enroll_res;
    }

    if($res == "error")
    {
        $reason = "报名出现错误，请重试";
    }

    $tpl = $my_app_pai->getView('welfare_enroll_success.tpl.htm');
}
else
{
    $payment_no = trim($_INPUT['payment_no']); //支付号
    $tpl = $my_app_pai->getView('fatherday_enroll_success.tpl.htm');
    //获取支付信息
    $payment_obj = POCO::singleton('pai_payment_class');
    $payment_info = $payment_obj->get_payment_info($payment_no);
    $reason = "";
    if( empty($payment_info) )
    {

        $res = "error";
        $reason = "原因：支付号有误";
    }
    else
    {

        $payment_status = intval($payment_info['status']);
        if( !in_array($payment_status, array(1, 8)) )
        {
            $res = "error";
            //echo "支付未成功";
            ///exit();
        }
        else
        {
            $channel_rid = intval($payment_info['channel_rid']); //报名ID、订单号
            $third_total_fee = $payment_info['third_total_fee']*1; //实收金额

            //echo "支付已成功 {$channel_rid} {$third_total_fee}";
            //exit();
            $res = "success";

        }
    }
    $tpl->assign("res",$res);

}




$tpl->assign("reason",$reason);
$tpl->output();
?>