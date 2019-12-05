<?php
/**
 * @Desc:   ��ѯ�������̼ұ���(����)
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/4
 * @Time:   17:02
 * version: 1.0
 */
//���ú���
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'operate_date_list');//Ȩ�޿���
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_report_operator_class.inc.php');

$report_operator_obj = new pai_report_operator_class();
$page_obj = new show_page();
$show_count = 30;

$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT."operate_date_list.tpl.htm");

$date = trim($_INPUT['date']);//Ĭ��ʱ��
$user_id = intval($_INPUT['user_id']);
$operator_id = intval($_INPUT['operator_id']);

$where_str = '';
$setParam  = array('operator_show'=>1);

//ʱ�䴦��
if(!preg_match("/\d\d\d\d-\d\d-\d\d/", $date) && !preg_match("/\d\d\d\d\d\d\d\d/", $date)) $date = date('Y-m-d',time()-24*3600);

//��ȡ���й����ߵ�ɸѡ����ʾ
$operator_arr = $report_operator_obj->get_operator_list_by_day(false,$date,0,$where_str,'GROUP BY operator_id','operator_id DESC','0,99999999',"operator_id");
if(!is_array($operator_arr))$operator_arr = array();
foreach($operator_arr as &$val)
{
    $val['operator_name'] = get_user_nickname_by_user_id($val['operator_id']);
    $val['selected'] = $operator_id == $val['operator_id'] ? true : false;
}

if(strlen($date) >0) $setParam['date'] = $date;
if($user_id >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "user_id = {$user_id}";
    $setParam['user_id'] = $user_id;
}
if($operator_id >0)$setParam['operator_id'] = $operator_id;


$page_obj->setvar($setParam);
$total_count = $report_operator_obj->get_operator_list_by_day(true,$date,$operator_id,$where_str,'','','',"distinct(user_id)");
$page_obj->set($show_count,$total_count);
$list = $report_operator_obj->get_operator_list_by_day(false,$date,$operator_id,$where_str,'GROUP BY user_id','success_price DESC,add_time DESC,user_id DESC',$page_obj->limit(),"SUM(success_price) AS success_price,SUM(success_count) AS success_count,SUM(cancel_count) AS cancel_count,login_count,reply_scale,user_id,operator_id");
if(!is_array($list)) $list = array();
foreach($list as &$v)
{
    $v['nickname'] = get_user_nickname_by_user_id($v['user_id']);
    $v['reply_scala_s'] = sprintf('%.4f',$v['reply_scale'])*100;
    $v['operator_name'] = get_user_nickname_by_user_id($v['operator_id']);
}

$tpl->assign('operator_arr',$operator_arr);
$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();