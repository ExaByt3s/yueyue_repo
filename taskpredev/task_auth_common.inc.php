<?php
/** 
 * 
 * tt
 * hudw
 * 2015-4-11
 * Ȩ�޿��Ƶ�common
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

	//  û�е�¼��ȥ��¼
	if (!$yue_login_id) 
	{
		header("Location: http://www.yueus.com/reg/login.php?r_url=".$r_url); 
		exit() ;
	}

	$user_obj = POCO::singleton ( 'pai_user_class' );

	$get_all_profile_obj = POCO::singleton('pai_task_profile_class');
	$is_supplier = $get_all_profile_obj->check_seller_by_user_id($yue_login_id);


	// if (!$is_supplier) 
	// {
	// 	$user_obj->logout();
	// 	js_pop_msg("�����̼��˺ŵ�¼Ŷ��", false,"http://www.yueus.com/reg/login.php?r_url=http%3A%2F%2Fwww.yueus.com%2Ftask%2F");
	// }

}
?>