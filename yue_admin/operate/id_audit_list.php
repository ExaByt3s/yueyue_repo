<?php

include_once 'common.inc.php';
include_once 'top.php';


$tpl = new SmartTemplate("id_audit_list.tpl.htm");

$page_obj = new show_page ();

$user_obj = POCO::singleton ( 'pai_user_class' );
$id_audit_obj = POCO::singleton('pai_id_audit_class');

$status  = $_INPUT['status'] ? $_INPUT['status'] :0;


$where  = "1 AND status={$status}";

$show_count = 40;

$page_obj->setvar ( array("status"=>$status));

$total_count = $id_audit_obj->get_audit_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $id_audit_obj->get_audit_list(false, $where, 'add_time DESC', $page_obj->limit());

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);    
	$list[$k]['nickname']  = get_user_nickname_by_user_id($val ['user_id'] );
	$list[$k]['cellphone'] = $user_obj->get_phone_by_user_id($val ['user_id'] );

}
     // var_dump($list);

$tpl->assign ( "page", $page_obj->output ( 1 ) );

$tpl->assign('list', $list);

$tpl->assign('status', $status);
//$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);

$tpl->output();
?>