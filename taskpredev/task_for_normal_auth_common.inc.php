<?php
/** 
 * 
 * tt
 * hudw
 * 2015-4-11
 * 权限控制的common for 普通用户
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

	//  没有登录跳去登录
	if (!$yue_login_id) 
	{
		header("Location: http://www.yueus.com/reg/login.php?r_url=".$r_url); 
		exit() ;
	}


}
?>