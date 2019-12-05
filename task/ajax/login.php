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
$yue_phone = intval($_INPUT['phone']);
$yue_password = $_INPUT['yue_password'];
$r_url = trim($_INPUT['r_url']);

$user_obj = POCO::singleton ( 'pai_user_class' );
$task_profile_obj = POCO::singleton ( 'pai_task_profile_class' );

$check = $task_profile_obj->check_seller_by_user_id($yue_phone);

if(!$yue_phone || !$yue_password)
{
	$output_arr['code'] = 0;
	$output_arr['message'] = "用户名或密码不能为空";
	die();
}

$ret = $user_obj->user_login($yue_phone, $yue_password);



if($ret >0)
{
	$output_arr['code'] = 1;
	$output_arr['message'] = "登录成功";
	
	if($check)
	{
		// 是商家 账号
		if($r_url)
		{
			$output_arr['r_url'] = $r_url;
		}
		else
		{
			$output_arr['r_url'] = urldecode('./list.php');
		}
	}
	else
	{
		// 消费者账号
		if($r_url)
		{
			$output_arr['r_url'] = $r_url;
		}
		else
		{
			$output_arr['r_url'] = urldecode('./zjxd.php');
		}
	}

	
}
else
{
	$output_arr['code'] = 0;
	$output_arr['message'] = "用户名或密码错误";
}

mobile_output($output_arr,false);
?>