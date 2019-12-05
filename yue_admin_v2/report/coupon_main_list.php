<?php
/**
 * @desc:   优惠券码列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/2
 * @Time:   13:59
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'coupon_main_list');//权限控制
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_add_coupon_class.inc.php');
$coupon_sn_obj = new pai_add_coupon_class();
$page_obj = new show_page();
$show_total = 30;

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'coupon_main_list.tpl.htm' );

$act = trim($_INPUT['act']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$coupon = trim($_INPUT['coupon']);
$user_id = (int)$_INPUT['user_id'];
$run_status = isset($_INPUT['run_status']) ? (int)$_INPUT['run_status'] : -1;

$where_str = '';
$setParam = array();
//参数整理
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME('%Y-%m-%d',begin_time)='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME('%Y-%m-%d',end_time)='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if(strlen($coupon)>0) $setParam['coupon'] = $coupon;
if($user_id >0) $setParam['user_id'] = $user_id;
if($run_status >=0) $setParam['run_status'] = $run_status;

//数据设置和查询
$page_obj->setvar($setParam);
$total_count = $coupon_sn_obj->get_coupon_main_list(true,$coupon,$user_id,$run_status,$where_str);
$page_obj->set($show_total,$total_count);
$list = $coupon_sn_obj->get_coupon_main_list(false,$coupon,$user_id,$run_status,$where_str,"id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();

$title = '套餐码列表';
if(strlen($title)>0) $setParam['title'] = $title;

$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();

