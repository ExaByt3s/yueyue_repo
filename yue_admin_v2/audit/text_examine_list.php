<?php
/**
 * @desc:   待审核文字
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/10
 * @Time:   9:34
 * version: 1.0
 */
include_once('common.inc.php');
$user_obj  = POCO::singleton('pai_user_class');//引入user_class.inc.php类
$text_examine_v2_obj = new pai_text_examine_v2_class();
$page_obj = new show_page();
$show_page = 20;

$tpl = new SmartTemplate( AUDIT_TEMPLATES_ROOT.'text_examine_list.tpl.htm' );

$act = trim($_INPUT['act']);

$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$user_id = intval($_INPUT['user_id']);
$cellphone = intval($_INPUT['cellphone']);
$type = trim($_INPUT['type']);

$type_list = $text_examine_v2_obj->get_type_list();//获取类型数组

$where_str = '';
$setParam = array();

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
if($user_id >0) $setParam['user_id'] = $user_id;
if(strlen($start_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d') >='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(strlen($end_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d') <='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

$page_obj->setvar($setParam);
$total_count = $text_examine_v2_obj->get_text_examine_list(true,$type,$user_id,$where_str);
$page_obj->set($show_page,$total_count);

$list = $text_examine_v2_obj->get_text_examine_list(false,$type,$user_id,$where_str,"add_time DESC,id DESC",$page_obj->limit());

if(!is_array($list)) $list = array();
foreach($list as &$v)
{
    $v['nickname'] = get_user_nickname_by_user_id($v['user_id']);
    $v['type_name'] = $text_examine_v2_obj->get_role_by_type($v['type']);
}


$tpl->assign('type_list',$type_list);
$tpl->assign($setParam);
$tpl->assign('list',$list);

$tpl->assign('page',$page_obj->output(true));
$tpl->output();



