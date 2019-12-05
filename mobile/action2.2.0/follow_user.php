<?php

/**
 * 关注用户
 * hdw 2014.9.15
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 接收参数
 */
$type = trim($_INPUT['type']);
$be_follow_user_id = intval($_INPUT['be_follow_user_id']);

$pai_obj = POCO::singleton('pai_user_follow_class');

switch($type)
{
	case 'follow':
		/*
		 * 添加关注
		 * 
		 * @param int    $follow_user_id    关注人用户ID
		 * @param int    $be_follow_user_id 被关注人用户ID
		 * 
		 * return bool 
		 */
		
		if($yue_login_id==$be_follow_user_id)
		{
			$msg = '不可以自己关注自己哦！';
			$ret = 0;
		}else
		{
		    $ret = $pai_obj->add_user_follow($yue_login_id, $be_follow_user_id);
	        $msg = '成功关注';
		}

		break;
	case 'no_follow':
		/*
		 * 取消关注
		 * 
		 * @param int    $follow_user_id    关注人用户ID
		 * @param int    $be_follow_user_id 被关注人用户ID
		 * 
		 * return bool
		 */
		$ret = $pai_obj->cancel_follow($yue_login_id, $be_follow_user_id);
		$msg = '取消关注';
		break;


}

$is_follow = $pai_obj->check_user_follow($yue_login_id, $be_follow_user_id);
$is_be_follow = $pai_obj->check_user_follow($be_follow_user_id, $yue_login_id);

if($is_follow && $is_be_follow)
{
	$follow_status=2;
}
elseif($is_follow)
{
	$follow_status = 1;
}
else
{
	$follow_status = 0;
}


$output_arr['is_follow'] = $follow_status;
$output_arr['code'] = $ret;
$output_arr['msg']  = $msg;

mobile_output($output_arr,false);

?>