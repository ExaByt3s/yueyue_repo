<?php

/** 
 * pc 
 * 头部
 * 汤圆
 * 2015-6-5
 * 引用资源文件定位，注意！确保引用路径争取
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

function _get_wbc_head($params)
{	
	$file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;

	if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
	{
		$tpl	 = $my_app_pai->getView($file_dir . "/head.tpl.htm",true);
	}
	else
	{
		$tpl = new SmartTemplate($file_dir . "/head.tpl.htm");
	}	

	$tpl_html = $tpl->result();

	return $tpl_html;
}



?>