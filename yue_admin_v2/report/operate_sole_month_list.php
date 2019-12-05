<?php
/**
 * @desc:   管理者单独管理商家月报表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/5
 * @Time:   10:00
 * version: 1.0
 */
//常用函数
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'operate_sole_month_list');//权限控制

include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_report_operator_class.inc.php');

$report_operator_obj = new pai_report_operator_class();
$page_obj = new show_page();
$show_count = 30;
$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT."operate_month_list.tpl.htm");

$operator_id = intval($yue_login_id);

$month = trim($_INPUT['month']);//默认时间
$user_id = intval($_INPUT['user_id']);

$where_str = '1';
$setParam  = array();

//时间处理
if(!preg_match("/\d\d\d\d-\d\d/", $month)) $month = date('Y-m',time()-24*3600);

if(strlen($month) >0) $setParam['month'] = $month;
if($user_id >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "user_id = {$user_id}";
    $setParam['user_id'] = $user_id;
}

$page_obj->setvar($setParam);
$total_count = $report_operator_obj->get_operator_list_by_month(true,$month,$operator_id,$where_str,'','','',"distinct(user_id)");
$page_obj->set($show_count,$total_count);

$list = $report_operator_obj->get_operator_list_by_month(false,$month,$operator_id,$where_str,'GROUP BY user_id','success_price DESC,add_time DESC,user_id DESC',$page_obj->limit(),"SUM(success_price) AS success_price,SUM(login_count) login_num,avg(reply_scale) AS reply_scala_s,SUM(success_count) AS success_count,SUM(cancel_count) AS cancel_count,user_id,operator_id");
if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $v['nickname'] = get_user_nickname_by_user_id($v['user_id']);
    $v['reply_scala_s'] = sprintf('%.4f',$v['reply_scala_s'])*100;
    $v['operator_name'] = get_user_nickname_by_user_id($v['operator_id']);
}

$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();