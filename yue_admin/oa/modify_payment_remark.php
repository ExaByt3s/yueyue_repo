<?php

include_once 'common.inc.php';
include_once 'top.php';

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

$order_id = $_INPUT['order_id'];

if($_POST)
{
	$update_data['payment_remark'] = $_INPUT['payment_remark'];


	$model_oa_order_obj->update_order($update_data,$order_id);
	
	echo "<script>alert('ÐÞ¸Ä³É¹¦');parent.location.href='payment_list.php';</script>";
		
	
	exit;
}

$tpl = new SmartTemplate ( "modify_payment_remark.tpl.htm" );


$order_info = $model_oa_order_obj->get_order_info($order_id);


$tpl->assign ( $order_info );



$tpl->assign ( 'YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER );
$tpl->output ();

?>