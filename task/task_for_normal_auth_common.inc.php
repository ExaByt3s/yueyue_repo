<?php
/** 
 * 
 * tt
 * hudw
 * 2015-4-11
 * Ȩ�޿��Ƶ�common for ��ͨ�û�
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!defined("DONT_CHECK_AUTH"))
{
	$r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_URL'];

	/**
	 * �жϿͻ���
	 */
	$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
	$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
	$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  

	if($_SERVER['QUERY_STRING'])
	{
		$r_url= $r_url .'?'. $_SERVER['QUERY_STRING'];
	}

	$r_url = urlencode($r_url);

	//  û�е�¼��ȥ��¼
	if (!$yue_login_id) 
	{
		if($__is_iphone || $__is_android)
		{
			header("Location: http://task.yueus.com/m/login.php?r_url=".$r_url); 
			exit() ;
		}
		else
		{
			// pc ��ת
			header("Location: http://www.yueus.com/reg/login.php?r_url=".$r_url); 
			exit() ;
		}

		
	}


}
?>