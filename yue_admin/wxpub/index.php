<?php
	/**
	 * 管理首页
	 * 
	 * @author Hai
	 * @copyright 2015-01-19
	 */	
	include_once('./common.inc.php');
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$bind_id= $_COOKIE['cur_bind_id'];
	$tpl 	= $my_app_pai->getView('index.tpl.htm');
	$header_arr  = array(); 
	$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
	$tpl->assign('bind_id',$bind_id);
	$tpl->assign('header_html', $header_html);	
	$tpl->output();

?>