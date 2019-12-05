<?php
/**
 * 约约摄影培训批量接受
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$mall_order_obj = POCO::singleton('pai_mall_order_class');
$order_list = $mall_order_obj->get_order_list_for_seller(110762, 0, pai_mall_order_class::STATUS_WAIT_CONFIRM, false, 'order_id ASC', '0,999');
if( empty($order_list) )
{
	die('没有待确认订单');
}

foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	
	$accept_rst = $mall_order_obj->accept_order_for_system($order_sn);
	echo "{$order_sn} {$accept_rst['message']}<br />\r\n";
}
