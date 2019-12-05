<?php
/** 
 * 
 * tt
 * hudw
 * 2015-4-11
 * 权限控制的common
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!defined("DONT_CHECK_AUTH"))
{
	$r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_URL'];

	if($_SERVER['QUERY_STRING'])
	{
		$r_url= $r_url .'?'. $_SERVER['QUERY_STRING'];
	}

	$r_url = urlencode($r_url);

	/**
	 * 判断客户端
	 */
	$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
	$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
	$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;

	//  没有登录跳去登录
	if (!$yue_login_id) 
	{
		if($__is_weixin || $__is_android || $__is_iphone)
		{
			header("Location: ./login.php?r_url=".$r_url); 
			exit() ;
		}
		else
		{
			header("Location: http://www.yueus.com/reg/login.php?r_url=".$r_url); 
			exit() ;
		}
		
	}

	$user_obj = POCO::singleton ( 'pai_user_class' );

	$get_all_profile_obj = POCO::singleton('pai_task_profile_class');
	$is_supplier = $get_all_profile_obj->check_seller_by_user_id($yue_login_id);

	if (!$is_supplier) 
	{
		$user_obj->logout();
		js_pop_msg("必须商家账号登录哦！", false,"http://www.yueus.com/reg/login.php?r_url=http%3A%2F%2Fwww.yueus.com%2Ftask%2F");
	}

}
?>