<?php
	
	include_once('./common.inc.php');
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$search_arr = array();
	$tpl = $my_app_pai->getView('bind_list.tpl.htm');
	$bind_list  = $weixin_helper_obj->get_bind_list_by_search($search_arr,false);
	$tpl->assign('bind_list', $bind_list);
	$tpl->output();

?>