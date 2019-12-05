<?php
/**
 * 处理面付过期的订单
 * @author Henry
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$cur_time = time(); //当前时间

$order_obj = POCO::singleton('pai_mall_order_class');

$result_str = date('Y-m-d H:i:s', $cur_time) . '|';

//处理订单没操作，超时
$where_str = "status IN (". pai_mall_order_class::STATUS_WAIT_PAY . ") AND expire_time>0 AND expire_time<={$cur_time}";
$order_list = $order_obj->get_order_list(0, -1, false, $where_str, 'expire_time ASC,order_id ASC', '0,99999999', '*', 'payment');
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	$is_auto_accept = intval($order_info['is_auto_accept']);
	$is_auto_sign = intval($order_info['is_auto_sign']);
	$status = intval($order_info['status']);
	
	if( $status===pai_mall_order_class::STATUS_WAIT_PAY ) //待付款，超时
	{
		$ret = $order_obj->close_order_for_system($order_sn); //系统关闭订单
	}
	else
	{
		$ret = array(
			'result' => 0,
			'message' => 'else',
		);
	}
	$result_str .= "1^{$order_sn}^{$status}^{$ret['result']}^{$ret['message']}|";
}

$result_str .= date('Y-m-d H:i:s');
echo $result_str;
