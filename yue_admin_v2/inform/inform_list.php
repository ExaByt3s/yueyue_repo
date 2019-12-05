<?php
/**
 * @desc:   举报列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/28
 * @Time:   15:57
 * version: 2.0
 */
include('common.inc.php');
check_auth($yue_login_id,'inform_list');//权限控制
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_log_inform_v2_class.inc.php');
$pai_log_inform_v2_obj = new pai_log_inform_v2_class(); //举报黑名单类
$page_obj = new show_page();
$show_count = 20;

$tpl = new SmartTemplate( TEMPLATES_ROOT."inform_list.tpl.htm" );

$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$to_informer = (int)$_INPUT['to_informer']; //举报者ID
$by_informer = (int)$_INPUT['by_informer']; //被举报者ID

$where_str = '';
$setParam = array();

//参数整理和拼成查询条件
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if($to_informer>0) $setParam['to_informer'] = $to_informer;
if($by_informer>0) $setParam['by_informer'] = $by_informer;

//获取数据库数据
$page_obj->setvar($setParam);
$total_count = $pai_log_inform_v2_obj->get_inform_list(true,$by_informer,$to_informer,$where_str);//总条数
$page_obj->set( $show_count, $total_count );
$list = $pai_log_inform_v2_obj->get_inform_list(false,$by_informer,$to_informer,$where_str,"add_time DESC,id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();

foreach ($list as &$v)
{
    $v['status'] = $pai_log_inform_v2_obj->get_info_by_to_informer_id($v['to_informer']);
}

$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign('total_count', $total_count);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();
