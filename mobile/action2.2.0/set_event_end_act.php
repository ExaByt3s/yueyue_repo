<?php 
ignore_user_abort(true);
set_time_limit(0);
/**
 * 活动结束
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}


$event_id = intval($_INPUT['event_id']);

/**
 * 
 * 活动结束
 * @param $event_id 
 * 
 * */
$ret = set_event_end_v2($event_id);

$output_arr['code'] = $ret;

if($ret==1){
	$output_arr['msg'] = '确认成功';
}elseif($ret==0){
	$output_arr['msg'] = '确认不成功';
}elseif($ret==-1){
	$output_arr['msg'] = '还没有人签到，请稍后再试';
}elseif($ret==-2){
	$output_arr['msg'] = '状态异常';
}
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>