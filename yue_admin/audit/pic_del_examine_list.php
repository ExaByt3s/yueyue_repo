<?php
/*
 * V2
 *已删除图片控制器
 *DATE:2015-07-27
*/
include_once 'common.inc.php';
check_authority(array('pic_examine'));
$tpl = new SmartTemplate("pic_del_examine_list.tpl.htm");
$page_obj = new show_page ();
$user_obj  = POCO::singleton('pai_user_class');//引入user_class.inc.php类
$pic_com_examine_obj = POCO::singleton ('pai_pic_examine_class');
$pic_examine_obj = POCO::singleton ('pai_pic_del_examine_class');
$show_count = 20;


$act        = trim($_INPUT['act']) ? trim($_INPUT['act']) : 'head';
$start_date = trim($_INPUT['start_date']);
$end_date   = trim($_INPUT['end_date']);
$user_id    = intval($_INPUT['user_id']);
$audit_id   = intval($_INPUT['audit_id']);
$year       = trim($_INPUT['year']) ? trim($_INPUT['year'])  : date('Y',time());
$month      = trim($_INPUT['month'])? $_INPUT['month'] : date('m',time());
$cellphone  = intval($_INPUT['cellphone']);

$where_str = "1 AND img_type = '".mysql_escape_string($act)."'";//查询条件
$setParam  = array('act'=> $act);
//分页传值
if(strlen($start_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(audit_time, '%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date']  = $start_date;
}
if (strlen($end_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(audit_time, '%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date']  = $end_date;
}
if ($user_id>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "user_id={$user_id}";
    $setParam['user_id']  = $user_id;
}
//对手机号码判断
if ($cellphone >0)
{
    $cellphone = preg_match ( '/^1\d{10}$/isU',$cellphone ) ? (int)$cellphone : '13800138000' ;
    $cuser_id  = $user_obj->get_user_id_by_phone($cellphone);
    $cuser_id = $cuser_id >0 ? $cuser_id : 0;
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "user_id = {$cuser_id} ";
    $setParam['cellphone']  = $cellphone;
}
if(strlen($year) >0)
{
    $setParam['year'] = $year;
}
if(strlen($month) >0)
{
    $setParam['month'] = $month;
}
//操作者ID
if ($audit_id>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str.= "audit_id={$audit_id}";
    $setParam['audit_id']  = $audit_id;
}

$page_obj->setvar ($setParam);
$total_count = $pic_examine_obj->get_pic_del_examine_list(true, $where_str, '', '', '', $year, $month);
$page_obj->set ( $show_count, $total_count );
$list = $pic_examine_obj->get_pic_del_examine_list(false, $where_str, $order_by = 'id DESC', $page_obj->limit(), $fields = '*', $year, $month);
foreach ($list as $key => $val)
{
    $list[$key]['nickname']   = get_user_nickname_by_user_id($val['user_id']);
    $list[$key]['audit_name'] = get_user_nickname_by_user_id($val['audit_id']);
    $list[$key]['img_url']    = $pic_com_examine_obj->change_img_url($val['img_url']);
    $list[$key]['thumb_url']  = yueyue_resize_act_img_url($pic_com_examine_obj->change_img_url($val['img_url']), 165);
    /*if($val['img_type'] == 'head')
    {
       $list[$key]['thumb_url']  = yueyue_resize_act_img_url($pic_com_examine_obj->change_img_url($val['img_url']), 165);
    }
    else
    {
        $list[$key]['thumb_url']  = yueyue_resize_act_img_url($pic_com_examine_obj->change_img_url($val['img_url']), 260);
    }*/

    $list[$key]['role_name']  = ($user_obj->check_role($val['user_id'])) == 'model' ? '模特' : '摄影师';
    $list[$key]['act'] = $act;
}
if ($act == 'works')
{
    $title = "已删除作品";
}
else
{
    $title = "已删除头像";
}
$tpl->assign('title', $title);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

?>