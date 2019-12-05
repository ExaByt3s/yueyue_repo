<?php 
ignore_user_abort(true);
/**
 * 活动取消
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}


$event_id = intval($_INPUT['event_id']);

/**
 * 
 * 活动取消
 * @param $event_id 
 * 
 * */
$ret = set_event_status_by_cancel($event_id);

$output_arr['code'] = $ret >0 ? 1 : 0;

if($ret==-2){
	$output_arr['msg'] = "活动取消失败";
}elseif($ret==-1){
	$output_arr['msg'] = "活动已有人签到，不能取消活动";
}elseif($ret==1){
	$output_arr['msg'] = "活动取消成功";
}

$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>