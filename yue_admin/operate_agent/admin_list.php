<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate("admin_list.tpl.htm");

$operate_obj = POCO::singleton('pai_mall_operate_agent_class');
$page_obj = new show_page ();

$act = $_INPUT['act'];
$id = $_INPUT['id'];
if($act=='del' && $id)
{
    $ret = $operate_obj->del_admin($id);
    if($ret['result']==1)
    {
        echo "<script>alert('É¾³ý³É¹¦');parent.location.href='admin_list.php';</script>";
    }
    else
    {
        echo "<script>alert('".$ret['message']."');</script>";
    }

    exit;
}

$admin_user_id = (int)$_INPUT['admin_user_id'];

if($admin_user_id)
{
    $where = " admin_user_id={$admin_user_id}";
}

$show_count = 20;

$page_obj->setvar (array("admin_user_id"=>$admin_user_id) );

$total_count = $operate_obj->get_admin_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $operate_obj->get_admin_list(false, $where, $page_obj->limit());


$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();

?>