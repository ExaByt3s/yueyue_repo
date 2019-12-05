<?php

/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
//$file_dir = dirname(__FILE__);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');	
//include_once($file_dir . '/../task_res_common.inc.php');

function _get_wbc_top_nav($attribs)
{	

    global $my_app_pai;
    global $yue_login_id;

	$file_dir = dirname(__FILE__);

	if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
	{
		$tpl	 = $my_app_pai->getView($file_dir . "/top_nav.tpl.html",true);
	}
	else
	{
		$tpl = new SmartTemplate($file_dir . "/top_nav.tpl.html");
	}	

	if (!$yue_login_id) 
	{
        $tpl->assign('no_login', '1');
    }

	$tpl->assign('cur_page', $attribs['cur_page']);

	$tpl_html = $tpl->result();

	return $tpl_html;
}


?>