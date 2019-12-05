<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/10
 * @Time:   18:55
 * version: 1.0
 */
include_once('common.inc.php');
$user_obj  = POCO::singleton('pai_user_class');//引入user_class.inc.php类
$text_examine_v2_obj = new pai_text_examine_v2_class();
$page_obj = new show_page();
$show_page = 20;

$tpl = new SmartTemplate( AUDIT_TEMPLATES_ROOT.'text_del_examine_list.tpl.htm' );

$month = trim($_INPUT['month']) ? trim($_INPUT['month']) : date('Y-m',time());
$start_audit_date = trim($_INPUT['start_audit_date']);
$end_audit_date = trim($_INPUT['end_audit_date']);
$audit_id = intval($_INPUT['audit_id']);
$user_id = intval($_INPUT['user_id']);
$cellphone = intval($_INPUT['cellphone']);
$type = trim($_INPUT['type']);

$where_str = '';
$setParam = array();

$type_list = $text_examine_v2_obj->get_type_list();//获取类型数组

if(strlen($month)>0)
{
    $setParam['month'] = $month;
    $date = $month.'-01';
}

if($cellphone >0)//手机号码判断
{
    if(!preg_match ( '/^1\d{10}$/isU',$cellphone)) js_pop_msg_v2('手机号码格式有误');
    $cuser_id  = $user_obj->get_user_id_by_phone($cellphone);
    $cuser_id = intval($cuser_id);
    if($cuser_id >0)
    {
        if(strlen($where_str)>0) $where_str .= ' AND ';
        $where_str .= "user_id={$cuser_id}";
    }
    $setParam['cellphone']  = $cellphone;
}
if(strlen($type)>0)
{
    foreach($type_list as &$v)
    {
        $v['selected'] = $type==$v['type'] ? true : false;
    }
    $setParam['type'] = $type;
}
if(strlen($start_audit_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(audit_time,'%Y-%m-%d') >='".mysql_escape_string($start_audit_date)."'";
    $setParam['start_audit_date'] = $start_audit_date;
}
if(strlen($end_audit_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .="DATE_FORMAT(audit_time,'%Y-%m-%d') <='".mysql_escape_string($end_audit_date)."'";
    $setParam['end_audit_date'] = $end_audit_date;
}
if($audit_id >0) $setParam['audit_id'] = $audit_id;
if($user_id >0) $setParam['user_id'] = $user_id;

$page_obj->setvar($setParam);
$total_count = $text_examine_v2_obj->get_text_del_list(true,$date,$type,$audit_id,$user_id,$where_str);
$page_obj->set($show_page,$total_count);
$list = $text_examine_v2_obj->get_text_del_list(false,$date,$type,$audit_id,$user_id,$where_str,'add_time DESC,id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();

foreach($list as &$val)
{
    $val['nickname'] = get_user_nickname_by_user_id($val['user_id']);
    $val['audit_name'] = get_user_nickname_by_user_id($val['audit_id']);
    $val['type_name'] = $text_examine_v2_obj->get_role_by_type($val['type']);
}

$tpl->assign('type_list',$type_list);
$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();
