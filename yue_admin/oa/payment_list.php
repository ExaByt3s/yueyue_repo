<?php

include_once 'common.inc.php';
include_once 'top.php';
//地区引用
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$tpl = new SmartTemplate ( "payment_list.tpl.htm" );

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$page_obj = new show_page ();

$show_count = 20;


$order_status = $_INPUT ['order_status'];
$order_id = $_INPUT ['order_id'];
$payment_status = $_INPUT ['payment_status'];
$begin_time = $_INPUT ['begin_time'];
$end_time = $_INPUT ['end_time'];
$select_time = $_INPUT ['select_time'];

$where = "order_status IN ('pay_confirm','close','refund','wait_shoot','wait_close')";

if($order_id)
{
    $where .= " AND order_id='{$order_id}'";
}

if ($order_status)
{
	$where .= " AND order_status='{$order_status}'";
}

if ($payment_status)
{
	$where .= " AND payment_status='{$payment_status}'";
}

if ($select_time && $begin_time && $end_time)
{
	$bt = strtotime ( $begin_time );
	$et = strtotime ( $end_time );
	if($select_time=='add_order')
	{
		$where .= " AND add_time BETWEEN {$bt} AND {$et}";
	}elseif($select_time=='pay_confrim')
	{
		$where .= " AND pay_time BETWEEN {$bt} AND {$et}";
	}
}



$page_obj->setvar ( array ("list_status" => $list_status, "order_id" => $order_id, "order_status" => $order_status, "begin_time" => $begin_time, "end_time" => $end_time, "cameraman_phone" => $cameraman_phone, "source" => $source ) );

$total_count = $model_oa_order_obj->get_order_list ( true, $where );

$page_obj->set ( $show_count, $total_count );

$list = $model_oa_order_obj->get_order_list ( false, $where, 'order_id DESC', $page_obj->limit () );

foreach ( $list as $k => $val )
{
	$list [$k] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
	
	if($val ['pay_time']){
		$list [$k] ['pay_time'] = date ( "Y-m-d H:i", $val ['pay_time'] );
	}
	
	$list [$k] ['city_name'] = get_poco_location_name_by_location_id ( $val ['location_id'], false, false );
	
	$list [$k] ['address'] = $city_name . $val ['date_address'];
	
	$list [$k] ['date_time'] = date ( "Y-m-d H:i", strtotime ( $val ['date_time'] ) );
	
	$list [$k] ['order_status'] = yue_oa_order_status ( $val ['order_status'] );
	
	$list [$k] ['total_price'] = $val ['hour']*$val ['budget'];
	
	$list [$k] ['user_id'] = (int)$user_obj->get_user_id_by_phone($val ['cameraman_phone']);
}

$tpl->assign ( "page", $page_obj->output ( 1 ) );

$tpl->assign ( 'list', $list );

$tpl->assign ( 'oa_role', $oa_role );

$tpl->assign ( 'payment_status', $payment_status );
$tpl->assign ( 'order_status', $order_status );

$tpl->assign ( 'begin_time', $begin_time );
$tpl->assign ( 'end_time', $end_time );
$tpl->assign ( 'select_time', $select_time );


$tpl->output ();

?>