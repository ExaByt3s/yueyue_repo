<?php
/**
 * @desc:   角色列表操作类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   9:29
 * version: 1.0
 */

include_once('common.inc.php');
check_auth($yue_login_id,'admin_role_list');//权限控制
$admin_role_index_obj  = POCO::singleton('pai_admin_role_index_class');//角色类
$page_obj = new show_page();
$show_total = 30;
$tpl = new SmartTemplate( 'admin_role_list.tpl.htm' );

$where_str = '';
$setParam  = array();

$total_count = $admin_role_index_obj->get_admin_role_index_list(true,$where_str);
$page_obj->set($show_total,$total_count);
$page_obj->setvar($setParam);
$list = $admin_role_index_obj->get_admin_role_index_list(false,$where_str,'sort DESC,role_id DESC',$page_obj->limit(),'*');
if(!is_array($list)) $list = array();


$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
