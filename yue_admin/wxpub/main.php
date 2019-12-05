<?php
	/**
	 * 菜单列表
	 * 
	 * @author Hai
	 * @copyright 2015-01-19
	 */	
	
	include_once('./common.inc.php');
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$tpl = $my_app_pai->getView('main.tpl.htm');
	$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',array(),true);
	$tpl->assign('header_html',$header_html);
	$tpl->output();

?>