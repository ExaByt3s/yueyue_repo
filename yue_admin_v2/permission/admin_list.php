<?php
/**
 * @desc:  管理员列表操作类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/27
 * @Time:   15:41
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'admin_list');//权限控制

$admin_index_obj  = POCO::singleton('pai_admin_index_class');//管理员类
$page_obj = new show_page();
$show_total = 30;
$tpl = new SmartTemplate( 'admin_list.tpl.htm' );

$user_id = intval($_INPUT['user_id']);
$real_name = trim($_INPUT['real_name']);
$department = trim($_INPUT['department']);

$where_str = '';
$setParam = array();

if($user_id >0)
{
    $setParam['user_id'] = $user_id;
}
if(strlen($real_name) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .="real_name like '%".mysql_escape_string($real_name)."%'";
    $setParam['real_name'] = $real_name;
}
if(strlen($department) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .="department like '%".mysql_escape_string($department)."%'";
    $setParam['department'] = $department;
}
$total_count = $admin_index_obj->get_admin_index_list(true,$user_id,-1,$where_str);
$page_obj->set($show_total,$total_count);
$page_obj->setvar($setParam);
$list = $admin_index_obj->get_admin_index_list(false,$user_id,-1,$where_str,'add_time DESC,user_id DESC',$page_obj->limit());
if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $v['nickname'] = get_user_nickname_by_user_id($v['user_id']);
}

$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();