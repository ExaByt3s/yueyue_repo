<?php
define('YUE_LOGIN_ORGANIZATION',1);
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
global $yue_login_id;
/*
 * 登录
 * @param String $phone String $yue_password
 * @return Obj
 */
$yue_phone = $_INPUT['phone'];
$yue_password = $_INPUT['yue_password'];


$user_obj = POCO::singleton ( 'pai_user_class' );
$task_profile_obj = POCO::singleton ( 'pai_task_profile_class' );


$check = $task_profile_obj->check_seller_by_user_id($yue_phone);

if($check)
{
	$ret = $user_obj->user_login($yue_phone, $yue_password);
	if($ret >0)
	{
		$output_arr['code'] = 1;
		$output_arr['message'] = "登录成功";
	}
	else
	{
		$output_arr['code'] = 0;
		$output_arr['message'] = "用户名或密码错误";
	}
}
else
{
	$output_arr['code'] = 0;
	$output_arr['message'] = "请用商家帐号登录";
}
mobile_output($output_arr,false);
?>