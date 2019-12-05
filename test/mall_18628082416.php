<?php
/**
 * 光线批量签到
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$mall_order_obj = POCO::singleton('pai_mall_order_class');
$order_list = $mall_order_obj->get_order_list_for_seller(113621, 0, pai_mall_order_class::STATUS_WAIT_SIGN, false, 'order_id ASC', '0,99');
if( empty($order_list) )
{
	die('没有待签到订单');
}

foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	
	$sign_rst = $mall_order_obj->sign_order_for_system($order_sn, false);
	echo "{$order_sn} {$sign_rst['message']}<br />\r\n";
}
