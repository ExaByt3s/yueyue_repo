<?php

include_once 'common.inc.php';
include_once 'top.php';

$import_order_obj = POCO::singleton ( 'pai_oa_mall_import_order_class' );

if($_INPUT['import_close']==1)
{
	file_get_contents("http://www.yueus.com/yue_admin/oa/mall_import_close.php");
	header("Location: http://www.yueus.com/yue_admin/oa/import_list.php");
	exit;
}

if($_INPUT['import_order']==1)
{
	file_get_contents("http://www.yueus.com/yue_admin/oa/mall_import_order.php");
	header("Location: http://www.yueus.com/yue_admin/oa/import_list.php");
	exit;
}


if($_INPUT['withdraw_type']=='app')
{
	
	$import_order_obj->update_order(array("withdraw"=>0), $_INPUT['id']);
	header("Location: http://www.yueus.com/yue_admin/oa/import_list.php");
	exit;
}
elseif($_INPUT['withdraw_type']=='offline')
{
	$import_order_obj->update_order(array("withdraw"=>1), $_INPUT['id']);
	header("Location: http://www.yueus.com/yue_admin/oa/import_list.php");
	exit;
}


$tpl = new SmartTemplate ( "import_list.tpl.htm" );

$page_obj = new show_page ();

$show_count = 20;


$total_count = $import_order_obj->get_order_list ( true, $where );

$page_obj->set ( $show_count, $total_count );

$list = $import_order_obj->get_order_list ( false, $where, 'id DESC', $page_obj->limit () );

foreach($list as $k=>$val)
{
	$list[$k]['import_time'] = date("Y-m-d H:i",$val['import_time']);
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );

$tpl->assign ( 'list', $list );



$tpl->output ();

?>