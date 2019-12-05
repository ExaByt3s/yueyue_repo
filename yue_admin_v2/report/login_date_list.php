<?php
/**
 * @desc:   登录数据统计
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/28
 * @Time:   9:25
 * version: 1.0
 */
include('common.inc.php');
$log_user_login_obj = POCO::singleton( 'pai_log_user_login_class' );
$page_obj = new show_page();
$show_page = 30;
$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'login_date_list.tpl.htm' );

$act = trim($_INPUT['act']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$role = trim($_INPUT['role']);
$type_id = intval($_INPUT['type_id']);

$where_str = '';
$setParam = array();

if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "add_time >='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($end_date)>0) $where_str .= ' AND ';
    $where_str .= "add_time <='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if(strlen($role)>0) $setparam['role'] = $role;
if($type_id >0) $setparam['type_id'] = $type_id;

$page_obj->setvar($setParam);
$total_count = $log_user_login_obj->get_log_user_login_list(true,$type_id,$where_str,'','','',"DISTINCT(add_time)");
$page_obj->set($show_page,$total_count);

$list = $log_user_login_obj->get_log_user_login_list(false,$type_id,$where_str,'GROUP BY add_time','add_time DESC,id DESC',$page_obj->limit(),"add_time,SUM(yuebuyer_7_login_num) AS yuebuyer_7_login_num,SUM(yuebuyer_30_login_num) AS yuebuyer_30_login_num,SUM(yueseller_7_login_num) AS yueseller_7_login_num,SUM(yueseller_30_login_num) AS yueseller_30_login_num");

if(strlen($role) <1)
{
   foreach($list as &$v)
   {
       if(strlen($role) <1)
       {
           $v['yuecount_30_login_num'] = $v['yuebuyer_30_login_num'] + $v['yueseller_30_login_num'] ;
           $v['yuecount_7_login_num'] = $v['yuebuyer_7_login_num'] + $v['yueseller_7_login_num'] ;
       }
       $v['total_num'] = $log_user_login_obj->get_user_num_by_date($v['add_time']);
   }
}

$tpl->assign('list', $list);
$tpl->assign($setParam);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();



