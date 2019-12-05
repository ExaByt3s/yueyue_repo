<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate("seller_list.tpl.htm");

$operate_obj = POCO::singleton('pai_mall_operate_agent_class');
$page_obj = new show_page ();


$act = $_INPUT['act'];
$id = $_INPUT['id'];
if($act=='del' && $id)
{
    $operate_obj->del_seller($id);
    echo "<script>alert('É¾³ý³É¹¦');parent.location.href='seller_list.php';</script>";
    exit;
}


$admin_user_id = (int)$_INPUT['admin_user_id'];
$seller_user_id = (int)$_INPUT['seller_user_id'];

$where = "1";
if($admin_user_id)
{
    $where .= " AND admin_user_id={$admin_user_id}";
}

if($seller_user_id)
{
    $where .= " AND seller_user_id={$seller_user_id}";
}

$show_count = 20;

$page_obj->setvar (array("admin_user_id"=>$admin_user_id,"seller_user_id"=>$seller_user_id) );

$total_count = $operate_obj->get_seller_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $operate_obj->get_seller_list(false, $where, $page_obj->limit());

foreach($list as $k=>$val)
{
    $admin_list = $operate_obj->get_admin_list(false, "admin_user_id=".$val['admin_user_id']);
    $list[$k]['admin_user'] = $admin_list[0]['name'];
    $list[$k]['time_text'] = date("Y-m-d",$val['begin_time']).'µ½'.date("Y-m-d",$val['end_time']);
}

$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();

?>