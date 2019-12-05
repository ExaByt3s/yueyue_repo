<?php
/** 
 * 
 * 活动浏览页
 * 
 * author 星星
 * 
 * 
 * 2015-3-3
 * 
 * 
 */
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$tpl = $my_app_pai->getView('party_pay_middle_jump.tpl.htm');


$payment_obj = POCO::singleton('pai_payment_class');

//获取支付信息
$payment_no = trim($_INPUT['payment_no']);
$event_id = (int)$_INPUT['event_id'];
$payment_info = $payment_obj->get_payment_info($payment_no);
if( empty($payment_info) )
{
    echo 'payment_no error';
    exit();
}
$event_info = unserialize($payment_info['channel_param']);
$event_id   = $event_info['event_id'];
if( $event_id < 1 ){

    echo 'event_id error';
    exit();

}

$payment_status = intval($payment_info['status']);
//检查支付状态，检查充值状态
$result = $payment_obj->return_recharge($payment_info);
if( $result['error']!==0 )
{
    
    //echo '支付产生错误';
    $res = "error";
}
else
{
    $res = "success";
    
}


$Party_global_header = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign('Party_global_header', $Party_global_header);
$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$footer_html = $my_app_pai->webControl('PartyFooter', array(), true);
$tpl->assign("res",$res);
$tpl->assign("event_id",$event_id);
$tpl->assign('Party_global_header', $Party_global_header);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);
$tpl->output();
?>