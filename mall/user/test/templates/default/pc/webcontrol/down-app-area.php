<?php

/** 
 * pc 
 * 头部
 * 汤圆
 * 2015-6-5
 * 引用资源文件定位，注意！确保引用路径争取
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



function _get_wbc_down_app_area($params)
{	
	$file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;

	if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
	{
		$tpl	 = $my_app_pai->getView($file_dir . "/down-app-area.tpl.htm",true);

		// 版本号
		$app_ver_str = file_get_contents('http://yp.yueus.com/download/version.txt');
		$tpl->assign('app_ver_str', '3.2.0');
	}
	else
	{
		$tpl = new SmartTemplate($file_dir . "/down-app-area.tpl.htm");
		
		// 版本号
		$app_ver_str = file_get_contents('http://yp.yueus.com/download/version.txt');
		$tpl->assign('app_ver_str', '3.2.0');
	}	

	$tpl_html = $tpl->result();

	return $tpl_html;
}


?>