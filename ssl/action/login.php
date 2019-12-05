<?php
/*
    登录页面
*/
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$account = trim(intval($_INPUT['account']));
$password = trim($_INPUT['password']);
$is_new_user = trim($_INPUT['is_new_user']);

$return_arr = array();
$user_id = 0;

$pai_user_obj = POCO::singleton('pai_user_class');	

// 用于标记是否是角色错误
$return_arr['is_role_error'] = false;

//检查参数
if( strlen($account)<1 || strlen($password)<1 )
{
	$return_arr['result'] = 500;
	$return_arr['msg'] = '帐号或密码为空';
	mobile_output($return_arr, false);
	exit();
}


//登录验证
$user_id = $pai_user_obj->user_login($account, $password);


if(!$user_id){

	$return_arr['result'] = 500;
	$return_arr['msg'] = '账号或密码错误';
	mobile_output($return_arr, false);
	exit();
	
}

$is_go_to_edit = false;



$return_arr['result'] = 200;
$return_arr['uid'] = $user_id;
$return_arr['user_id'] = $user_id;
$return_arr['access_token'] = $user_id;
$return_arr['access_token_secret'] = $user_id;
$return_arr['msg'] = '登录成功';
$return_arr['is_go_to_edit'] = $is_go_to_edit;

$return_arr['user_info'] = $pai_user_obj->get_user_info_by_user_id($user_id);

mobile_output($return_arr,false);

