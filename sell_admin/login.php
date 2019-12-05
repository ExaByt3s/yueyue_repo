<?php 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$seller_id = (int)$_INPUT['seller_id'];
$pwd = $_INPUT['pwd'];

if($seller_id && $pwd)
{
	// 做登录请求
	$payment_obj = POCO::singleton('pai_payment_class');
	$user_obj = POCO::singleton('pai_user_class');

	$user_info = $user_obj->get_user_info($seller_id);

	if(!$user_info)
	{

		$output_arr['message'] = '该账号不存在';
		$output_arr['code'] = 0;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
		exit();
	}


	$user_id = $user_obj->user_login($user_info['cellphone'], $pwd);

	if(!$user_id)
	{
		$output_arr['message'] = '账号或密码错误';
		$output_arr['code'] = 0;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
		exit();
	}


	$check_seller = $payment_obj->get_card_seller_info($user_id);

	if(!$check_seller)
	{
		$output_arr['message'] = '账号不是商家账号';
		$output_arr['code'] = 0;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
		exit();
	}
	else
	{
		$check = $payment_obj->check_is_card_seller($user_id);
		if(!$check)
		{
			$output_arr['message'] = '该账号已停用';
			$output_arr['code'] = 0;
			$output_arr['data'] = '';
			mobile_output($output_arr,false); 
			exit();
		}
	
		setcookie("yue_seller_admin", 1, time()+600, "/", "yueus.com");
		//登录成功
		$output_arr['message'] = '登录成功';
		$output_arr['code'] = 1;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
	}
}
else
{
	if($yue_login_id && $_COOKIE['yue_seller_admin'])
	{
		header("Location: list.php");
	}

	include_once($file_dir. '/./webcontrol/head.php');
	$tpl = $my_app_pai->getView('login.tpl.html');
	// 公共样式和js引入
	$head_html = _get_wbc_head();

	$tpl ->assign('head_html',$head_html);
	$tpl->output();
}




?>