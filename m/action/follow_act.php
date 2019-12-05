<?php

/**
 * 关注
 * hdw 2014.9.10
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_follow_obj = POCO::singleton('pai_event_follow_class');//活动关注

$event_id = intval($_INPUT['event_id']);

$add_follow = intval($_INPUT['add_follow']);

if($add_follow)
{
	/*
	* 添加活动关注
	* 
	* @param int    $event_id    活动ID
	* @param int    $user_id     用户ID
	* 
	* return bool 
	*/
	$ret = $pai_follow_obj->add_event_follow($event_id, $yue_login_id);

	$msg = '关注成功';
}
else
{
	/*
	* 取消关注
	* 
	* @param int    $event_id    活动ID
	* @param int    $user_id     用户ID
	* 
	* return bool
	*/
	$ret = $pai_follow_obj->cancel_follow($event_id, $yue_login_id);	

	$msg = '取消关注成功';
}

$output_arr['code'] = $ret;
$output_arr['msg']  = mb_convert_encoding($msg, 'gbk','utf-8');

mobile_output($output_arr,false);

?>