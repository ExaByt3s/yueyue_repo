<?php
/**
 * 
 *
 * @author hudingwen
 * @version 1.0
 * @copyright , 30 June, 2015
 * @package default
 */	
	
/**
 * 获取单条订单完整信息
 */	

include_once('../common.inc.php');

// 没有登录的处理
if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = '尚未登录,非法操作';
	$output_arr['data'] = array();
	exit();
}

// 订单类
$mall_order_obj = POCO::singleton('pai_mall_order_class');

// 订单sn
$order_sn = trim($_INPUT['order_sn']);

// 获取订单详情
$ret = $mall_order_obj->get_order_full_info($order_sn);

if($ret)
{
	$output_arr['code'] = 1;
	$output_arr['msg']  = 'Success';
}
else
{
	$output_arr['code'] = 0;
	$output_arr['msg']  = 'Error';
}

$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);	
?>