<?php
/**
 * 获取用户的等级
 *
 * author 黄石汉
 *
 * 2015-10-21
 */
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');

//首先 判断是否为 AJAX 请求
if( !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest' )
{
	$res_arr = array("ajax_status" => -1, "level" => -1);
	echo json_encode($res_arr);
	exit();
}

//加载 验证操作类 首先验证 taken令牌是否正确，如果验证不成功，返回json并退出脚本
$validation_code_obj = POCO::singleton('validation_code_class');
$token_ajax = trim($_INPUT['token']);
if( strlen($token_ajax)<1 || $validation_code_obj->get_hash()!=$token_ajax )
{	
	$res_arr = array("ajax_status" => -1, "level" => -1);
	echo json_encode($res_arr);
	exit();
}

//接收action的值 9de4a97425678c5b1288aa70c1669a64  = md5('register')，判断action数据匹不匹配md5值  # 正则检验手机号正不正确
$action = trim($_INPUT['action']);
$yue_phone = (int)$_INPUT['phone_num'];
if( !in_array($action, array( md5("register"), md5("change_pwd"))) || !preg_match('/^1\d{10}$/', $yue_phone) )
{
	$res_arr = array("ajax_status" => -1, "level" => -1);
	echo json_encode($res_arr);
	exit();
}

//检测手机是否已注册了，如果注册了就返回信息
$user_obj = POCO::singleton ( 'pai_user_class' );
if ( $action == md5("register") )
{
	$ret = $user_obj->check_cellphone_exist($yue_phone);
	if ( $ret )
	{
		$msg = mb_convert_encoding("该手机号已经注册过约约！", 'utf-8', 'gbk');
		$res_arr = array("ajax_status" => -2, "level" => -2, "msg" => $msg);
		echo json_encode($res_arr);
		exit();
	}
}

//检测手机是否已注册了，如果注册了就返回信息
if ( $action == md5("change_pwd") )
{
	$ret = $user_obj->check_cellphone_exist($yue_phone);
	if ( !$ret )
	{
		$msg = mb_convert_encoding("该手机号还没有注册约约！", 'utf-8', 'gbk');
		$res_arr = array("ajax_status" => -2, "level" => -2, "msg" => $msg);
		echo json_encode($res_arr);
		exit();
	}
}


//调用get_level()方法验证等级 - 此方法有判断了是否wap与web pc请求 返回值为0时，表示微信请求，2为wap与web pc请求
$level = $validation_code_obj->get_level();
if( $level==1 )
{
	//暂时当0处理
}
elseif( $level==2 )
{
	$res_arr = array("ajax_status" => 1, "level" => 2);
	echo json_encode($res_arr);
	exit();
}

//$level为0的情况下
$pai_sms_obj = POCO::singleton('pai_sms_class');
if( $action==md5("register") )
{
	$ret = $pai_sms_obj->send_phone_reg_verify_code($yue_phone);
}
elseif( $action==md5("change_pwd") )
{
	$group_key = 'G_PAI_USER_PASSWORD_VERIFY';
	$ret = $pai_sms_obj->send_verify_code($yue_phone, $group_key, array());
}
else 
{
	$ret = false;
}
//有数据表示发送成功 返回 1 表示发送成功 2 表示有发送，但没有成功
if( $ret )
{
	$res_arr = array("ajax_status" => 1, "level" => 0 , 'send_status'=>1);
}
else
{
	$res_arr = array("ajax_status" => 1, "level" => 0 , 'send_status'=>2);
}
echo json_encode($res_arr);
