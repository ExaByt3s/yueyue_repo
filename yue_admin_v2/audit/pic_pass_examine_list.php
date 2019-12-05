<?php
/**
 * @desc:   已经审核的数据
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/8
 * @Time:   17:30
 * version: 2.0
 */
include_once('common.inc.php');
$user_obj  = POCO::singleton('pai_user_class');//引入user_class.inc.php类
$pic_examine_v2_obj = new pai_pic_examine_v2_class();
$page_obj = new show_page();
$show_page = 20;

$tpl = new SmartTemplate( AUDIT_TEMPLATES_ROOT.'pic_pass_examine_list.tpl.htm' );

$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']) ? trim($_INPUT['month']) : date('Y-m',time());

$start_audit_date = trim($_INPUT['start_audit_date']);
$end_audit_date = trim($_INPUT['end_audit_date']);
$audit_id = intval($_INPUT['audit_id']);
$user_id = intval($_INPUT['user_id']);
$cellphone = intval($_INPUT['cellphone']);

$where_str = '';
$setParam = array();

if(!in_array($act,$type_img_arr)) js_pop_msg_v2('非法操作');
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

if(strlen($act)>0) $setParam['act'] = $act;
if(strlen($month)>0)
{
    $setParam['month'] = $month;
    $date = $month.'-01';
}

if(strlen($start_audit_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(audit_time,'%Y-%m-%d')>='".mysql_escape_string($start_audit_date)."'";
    $setParam['start_audit_date'] = $start_audit_date;
}
if(strlen($end_audit_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(audit_time,'%Y-%m-%d')<='".mysql_escape_string($end_audit_date)."'";
    $setParam['end_audit_date'] = $end_audit_date;
}
if($audit_id >0)  $setParam['audit_id'] = $audit_id;
if($user_id >0)  $setParam['user_id'] = $user_id;

$page_obj->setvar($setParam);
$total_count = $pic_examine_v2_obj->get_pic_pass_list(true,$date,$act,$audit_id,$user_id,$where_str);
$page_obj->set($show_page,$total_count);

$list = $pic_examine_v2_obj->get_pic_pass_list(false,$date,$act,$audit_id,$user_id,$where_str,'audit_time DESC,id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $v['role'] = $pic_examine_v2_obj->get_send_to_role($v['img_type'],$v['img_url']);
    $v['nickname']   = get_user_nickname_by_user_id($v['user_id']);
    $v['img_url']    = $pic_examine_v2_obj->change_img_url($v['img_url']);
    $v['thumb_url']  = yueyue_resize_act_img_url($v['img_url'], 165);
    $v['audit_name'] = get_user_nickname_by_user_id($v['audit_id']);
    $v['act'] = $act;
}
if($act == 'works')
{
    $setParam['title'] = "已审核作品";
}
elseif($act == 'merchandise')
{
    $setParam['title'] = '已审核商城图片';
}
else
{
    $setParam['title'] = "已审核头像";
}


$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(1));
$tpl->output();