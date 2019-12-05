<?php
/**
 * 文字待审核控制器
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-10 13:33:51
 * @version 2
 * 添加了备注
 */

include_once 'common.inc.php';
check_authority(array('text_examine'));
$page_obj = new show_page ();
$show_count = 20;
$user_obj  = POCO::singleton('pai_user_class');//引入user_class.inc.php类
$text_examine_obj = POCO::singleton ('pai_text_examine_class');
$tpl = new SmartTemplate("text_examine_list.tpl.htm");


$start_date = trim($_INPUT['start_date']);
$end_date   = trim($_INPUT['end_date']);
$user_id    = intval($_INPUT['user_id']);
$cellphone  = intval($_INPUT['cellphone']);
$type       = trim($_INPUT['type']);

$where_str = "1 AND before_edit <> after_edit AND after_edit<>'这人很懒，什么都没留下'";
$setParam = array();
if (strlen($start_date)>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time, '%Y-%m-%d') >= '".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if (strlen($end_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time, '%Y-%m-%d') <= '".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if ($user_id >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .="user_id={$user_id}";
    $setParam['user_id'] = $user_id;
}
if ($cellphone >0)//对手机号码判断
{
    $cuser_id  = $user_obj->get_user_id_by_phone($cellphone);
    $cuser_id = intval($cuser_id);
    $cuser_id  = $cuser_id >0 ? $cuser_id : 0;
    if ($user_id)
    {
        $where_str .= $user_id != $cuser_id ? " OR user_id = {$cuser_id} " : "";
    }
    else
    {
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str .= "user_id = {$cuser_id} ";
    }
    $setParam['cellphone']  = $cellphone;
}
//类型判断
if(strlen($type) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "type='".mysql_escape_string($type)."'";
    $setParam['type'] = $type;
}
$page_obj->setvar ($setParam);

$total_count = $text_examine_obj->get_text_examine_list(true, $where_str);
$page_obj->set ( $show_count, $total_count );
$list = $text_examine_obj->get_text_examine_list(false, $where_str, $order_by = 'user_id DESC', $page_obj->limit(), $fields = '*');
foreach ($list as $key => $val)
{
    $list[$key]['after_edit'] = $text_examine_obj->add_red($val['before_edit'], $val['after_edit']);
    $list[$key]['nickname']   = get_user_nickname_by_user_id($val['user_id']);
    $list[$key]['role']       = $user_obj->check_role($val['user_id']);
}
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

?>