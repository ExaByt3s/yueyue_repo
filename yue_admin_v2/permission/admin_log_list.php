<?php
/**
 * @desc:   管理员操作日志
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/30
 * @Time:   18:17
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'admin_log');//权限控制

$admin_log_obj  = POCO::singleton('pai_admin_log_class');//操作log
$page_obj = new show_page();
$show_total = 30;
$tpl = new SmartTemplate( 'admin_log_list.tpl.htm' );

$module = trim($_INPUT['module']);
$action = trim($_INPUT['action']);
$operate_id = intval($_INPUT['operate_id']);
$start_time = trim($_INPUT['start_time']);
$end_time = trim($_INPUT['end_time']);

$where_str = '';
$setParam = array();

if(strlen($module)>0)
{
    $setParam['module'] = $module;
}
if(strlen($action)>0)
{
    $setParam['action'] = $action;
}
if($operate_id >0)
{
    $setParam['operate_id'] = $operate_id;
}
if(strlen($start_time) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') >='".mysql_escape_string($start_time)."'";
    $setParam['start_time'] = $start_time;
}
if(strlen($end_time) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') <='".mysql_escape_string($end_time)."'";
    $setParam['end_time'] = $end_time;
}

$total_count = $admin_log_obj->get_admin_log_list(true,$module,$action,$operate_id,$where_str);
$page_obj->set($show_total,$total_count);
$page_obj->setvar($setParam);
$list = $admin_log_obj->get_admin_log_list(false,$module,$action,$operate_id,$where_str,'add_time DESC,id DESC',$page_obj->limit(), '*');
if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $v['operate_name'] = get_user_nickname_by_user_id($v['operate_id']);
    $v['log_desc'] = poco_cutstr($v['log'],100,'...');
}
$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

