<?php
/*
 * V2
 *Í¼Æ¬´ýÉóºË¿ØÖÆÆ÷
 *DATE 2015-07-27
 *
*/
include_once 'common.inc.php';
$tpl = new SmartTemplate("pic_examine_list.tpl.htm");
$page_obj = new show_page ();
$user_obj  = POCO::singleton('pai_user_class');
$pic_examine_obj = POCO::singleton ('pai_pic_examine_class');
$show_count   = 20;


$act          = trim($_INPUT['act']) ? trim($_INPUT['act']) : 'head';
$start_date   = trim($_INPUT['start_date']);
$end_date     = trim($_INPUT['end_date']);
$user_id      = intval($_INPUT['user_id']);
$cellphone    = intval($_INPUT['cellphone']);

$setval = array();
$where_str = "1 AND img_type = '{$act}'";
$setval['act'] = $act;
if(strlen($start_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str  .= "DATE_FORMAT(add_time, '%Y-%m-%d') >= '".mysql_escape_string($start_date)."'";
    $setval['start_date'] = $start_date;

}
if(strlen($end_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str  .= "DATE_FORMAT(add_time, '%Y-%m-%d') <= '".mysql_escape_string($end_date)."'";
    $setval['end_date'] = $end_date;
}
if ($user_id >0)
{
    if(strlen($where_str) >0) $where_str .=' AND ';
    $where_str .="user_id={$user_id}";
    $setval['user_id'] = $user_id;
}
//¶ÔÊÖ»úºÅÂëÅÐ¶Ï
if ($cellphone >0)
{
    $cellphone = preg_match ( '/^1\d{10}$/isU',$cellphone ) ? (int)$cellphone : '13800138000' ;
    $cuser_id  = $user_obj->get_user_id_by_phone($cellphone);
    $cuser_id = !empty($cuser_id) ? $cuser_id : 0;
    if(strlen($where_str) >0) $where_str .=' AND ';
    $where_str .= "user_id = {$cuser_id} ";
    $setval['cellphone']  = $cellphone;
}
$page_obj->setvar($setval);
//var_dump($where_str);
$total_count = $pic_examine_obj->get_pic_examine_list(true, $where_str);
//print_r($total_count);exit;
$page_obj->set ( $show_count, $total_count );
$list = $pic_examine_obj->get_pic_examine_list(false, $where_str, $order_by = 'id DESC', $page_obj->limit(), $fields = '*');
foreach ($list as $key => $val)
{
    $list[$key]['nickname']   = get_user_nickname_by_user_id($val['user_id']);
    $list[$key]['img_url']    = $pic_examine_obj->change_img_url($val['img_url']);
    $list[$key]['thumb_url']  = yueyue_resize_act_img_url($pic_examine_obj->change_img_url($val['img_url']), 165);
    $list[$key]['role_name']  = ($user_obj->check_role($val['user_id'])) == 'model' ? 'Ä£ÌØ' : 'ÉãÓ°Ê¦';
}
if ($act == 'works')
{
    $title = "´ýÉóºË×÷Æ·";
}
elseif($act == 'merchandise')
{
    $title = 'ÉÌ³ÇÍ¼Æ¬ÉóºË';
}
else
{
    $title = "´ýÉóºËÍ·Ïñ";
}
$tpl->assign('title', $title);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('act', $act);
$tpl->assign($setval);
$tpl->assign('list', $list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

?>