<?php
ignore_user_abort(true);
/**
 * 信用等级列表
 */

include_once('../common.inc.php');

/**
 * 页面接收参数
 */


// 没有登录的处理
if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = '尚未登录,非法操作';
	$output_arr['data'] = array();
	mobile_output($output_arr,false);
	exit();
}


$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$level_list = $user_level_obj->level_list($yue_login_id);

$level_detail = $user_level_obj->level_detail($yue_login_id);

$output_arr['list'] = $level_list;

$output_arr['data'] = $level_detail;

mall_mobile_output($output_arr,false);

?>