<?php
/**
 * 任务，退还优惠券
 * @author Henry
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$payment_obj = POCO::singleton('pai_payment_class');
$coupon_obj = POCO::singleton('pai_coupon_class');

$channel_module = 'task_request';
$deal_time = time() - 1800;

//获取报价列表，使用了优惠券，未支付
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$where_str = "status=1 AND is_pay=0 AND is_use_coupon=1 AND use_coupon_time<={$deal_time}";
$quotes_list = $task_quotes_obj->get_quotes_list(0, 0, false, $where_str, 'quotes_id ASC', '0,1000');
foreach($quotes_list as $quotes_info)
{
	$quotes_id = intval($quotes_info['quotes_id']);
	$request_id = intval($quotes_info['request_id']);
	if( $quotes_id<1 || $request_id<1 ) continue;
	
	$ret = $coupon_obj->not_use_coupon_by_oid($channel_module, $quotes_id);
	if( $ret )
	{
		$data = array(
			'discount_price' => 0,
			'is_use_coupon' => 0,
			'use_coupon_time' => 0,
		);
		$task_quotes_obj->update_quotes($data, $quotes_id);
	}
	
	//日志
	$log_arr = array(
		'quotes_info' => $quotes_info,
		'ret' => $ret,
	);
	pai_log_class::add_log($log_arr, 'return_coupon', 'pay_tt');
}

echo '任务，退还优惠券 ' . date('Y-m-d H:i:s');
