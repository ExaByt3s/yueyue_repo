<?php
ignore_user_abort(true);
/**
 * 更新同意状态
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$date_id = intval($_INPUT['date_id']) ;
$agree_status = trim($_INPUT['agree_status']);


if(!$yue_login_id)
{
	die('no login');
}
//$agree_status传agree或disagree
$ret = update_agree_status($date_id, $agree_status);

if($ret==1){
	$msg = "成功";
}elseif($ret==-1){
	$msg = "你已回复摄影师取消申请";
}elseif($ret==-2){
	$msg = "系统状态异常";
}elseif($ret==-3){
	$msg = "活动已结束";
}

$output_arr['msg'] = $msg;

$output_arr['data'] = $ret;


mobile_output($output_arr,false);

?>