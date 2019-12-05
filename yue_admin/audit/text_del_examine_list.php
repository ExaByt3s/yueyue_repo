<?php

/**
 * 已删除文字控制器V2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-10 14:59:37
 * @version 2
 * 添加了备注|完善了界面
 */
include_once 'common.inc.php';
check_authority(array('text_examine'));
$user_obj  = POCO::singleton('pai_user_class');//引入user_class.inc.php类
$text_com_examine_obj = POCO::singleton ('pai_text_examine_class');
$text_examine_obj     = POCO::singleton ('pai_text_del_examine_class');
$page_obj = new show_page ();
$show_count = 20;
$tpl = new SmartTemplate("text_del_examine_list.tpl.htm");

$start_date = trim($_INPUT['start_date']);
$end_date   = trim($_INPUT['end_date']);
$user_id    = intval($_INPUT['user_id']);
$audit_id   = intval($_INPUT['audit_id']);
$year       = trim($_INPUT['year'])?trim($_INPUT['year'])  : date('Y',time());
$month      = trim($_INPUT['month']) ? trim($_INPUT['month']) : date('m',time());
$cellphone  = intval($_INPUT['cellphone']);
$type       = trim($_INPUT['type']);


//查询条件
$where_str = '';
$setParam = array();
if (strlen($start_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(audit_time, '%Y-%m-%d') >= '".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if (strlen($end_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(audit_time, '%Y-%m-%d') <= '".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if ($user_id >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "user_id={$user_id}";
    $setParam['user_id'] = $user_id;
}
//对手机号码判断
if ($cellphone>0)
{
    $cuser_id  = $user_obj->get_user_id_by_phone($cellphone);
    $cuser_id = $cuser_id >0 ? $cuser_id : 0;
    if ($user_id >0)
    {
        $where_str .= $user_id != $cuser_id ? " OR user_id = {$cuser_id} " : "";
    }
    else
    {
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str .= "user_id = {$cuser_id}";
    }
    $setParam['cellphone'] = $cellphone;
}
if ($audit_id >0)//审核人ID
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str.= "audit_id={$audit_id}";
    $setParam['audit_id'] = $audit_id;
}
if(strlen($type) >0)//类型
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "type = '".mysql_escape_string($type)."'";
    $setParam['type'] = $type;
}
if(strlen($year) >0) $setParam['year'] = $year;
if(strlen($month) >0) $setParam['month'] = $month;

$page_obj->setvar ($setParam);
$total_count = $text_examine_obj->get_text_del_examine_list(true, $where_str,$year, $month);
$page_obj->set ( $show_count, $total_count );
$list = $text_examine_obj->get_text_del_examine_list(false, $where_str, $year, $month, $order_by = 'user_id DESC,id DESC', $page_obj->limit(), $fields = '*');
foreach ($list as $key => $val)
{
    $list[$key]['nickname']   = get_user_nickname_by_user_id($val['user_id']);;
    $list[$key]['audit_name'] = get_user_nickname_by_user_id($val['audit_id']);;
    $list[$key]['role']       = $user_obj->check_role($val['user_id']);
}
$tpl->assign ("page", $page_obj->output ( 1 ) );
$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

?>