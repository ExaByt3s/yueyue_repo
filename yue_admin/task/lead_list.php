<?php
include_once 'common.inc.php';
$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."lead_list.tpl.htm" );

$lead_obj = POCO::singleton('pai_task_lead_class');

$request_id = (int)$_INPUT ['request_id'];


$list = $lead_obj->get_lead_list_by_request_id($request_id,false, '', '0,100000');

foreach($list as $k=>$val)
{
	$list[$k]['expire_time'] = date("Y-m-d H:i:s",$val['expire_time']);
	$list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
	$list[$k]['service_name'] = $service_name[$val['service_id']];	
	$list[$k]['lead_status_name'] = $val['lead_status']?"рямф╪Ж":"н╢иС╨к";
}

$tpl->assign ( 'list', $list );
$tpl->assign ( 'lead_type', $lead_type);
$tpl->output ();
?>