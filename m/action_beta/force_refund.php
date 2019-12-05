<?php
ignore_user_abort(true);
/**
 * 强制退款
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$date_id = intval($_INPUT['date_id']) ;


if(!$yue_login_id)
{
	die('no login');
}

$ret = update_force_refund_status($date_id);


if($ret==1){
	$msg = "成功";
}elseif($ret==-1){
	$msg = "模特同意状态异常";
}elseif($ret==-2){
	$msg = "退款失败，请联系管理员";
}elseif($ret==-3){
	$msg = "系统状态异常";
}elseif($ret==-4){
	$msg = "活动已结束";
}elseif($ret==-5){
	$msg = "活动券已被扫描，不能退款了";
}elseif($ret==-6){
	$msg = "模特已退款了";
}

$output_arr['msg'] = $msg;

$output_arr['data'] = $ret;


mobile_output($output_arr,false);

?>