<?php
/**
 * @desc:   单条记录log数据
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/30
 * @Time:   16:03
 * version: 1.0
 */
include_once('common.inc.php');
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_send_single_message_class.inc.php');
$single_message_obj = new pai_send_single_message_class();
$page_obj = new show_page();
$show_total = 20;


$tpl = new SmartTemplate( TEMPLATES_ROOT.'single_log_list.tpl.htm');

$act = trim($_INPUT['act']);
$role = trim($_INPUT['role']);
$user_id = (int)$user_id;
$add_id = (int)$_INPUT['add_id'];
$type = trim($_INPUT['type']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

//参数整理和sql语句的构建
$where_str = '';
$setParam = array();

if(strlen($role)>0) $setParam['role'] = $role;
if($add_id >0) $setParam['add_id'] = $add_id;
if(strlen($type)>0) $setParam['type'] = $type;

if($user_id >0)//用户ID
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "user_id = {$user_id}";
    $setParam['user_id'] = $user_id;
}

if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

$page_obj->setvar($setParam);
$total_count = $single_message_obj->get_single_list(true,$role,$add_id,$type,$where_str);
$page_obj->set($show_total,$total_count);
$list = $single_message_obj->get_single_list(false,$role,$add_id,$type,$where_str,"add_time DESC,id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $v['send_name']  = get_user_nickname_by_user_id($v['add_id']);
    $v['desc']       = poco_cutstr($v['content'], 20, '....');
    $v['card_text1'] = poco_cutstr($v['card_text1'], 20, '....');
}

$tpl->assign('total_count',$total_count);
$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();


