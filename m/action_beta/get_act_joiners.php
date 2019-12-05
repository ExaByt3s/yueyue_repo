<?php
/**
 * 获取活动报名网友
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$event_id = intval($_INPUT['event_id']);

//$event_id = 40150;

$ret = get_mark_list_v2($event_id);

/*
* 是否已关注该活动
* 
* @param int    $event_id    活动ID
* @param int    $user_id     用户ID
* 
* return bool
*/

if($yue_login_id)
{
	$pai_follow_obj = POCO::singleton('pai_event_follow_class');//活动关注

	$is_follow = $pai_follow_obj->check_event_follow($event_id, $yue_login_id);
}


$output_arr['list'] = $ret;

$output_arr['is_follow'] = $is_follow;

$output_arr['event_title'] = $ret[0]['event_title'];
$output_arr['event_organizers'] = $ret[0]['event_organizers'];
$output_arr['event_status'] = $ret[0]['event_status'];

mobile_output($output_arr,false);

?>