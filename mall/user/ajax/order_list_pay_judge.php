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
 * 订单列表
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

$order_sn = intval($_INPUT['order_sn']);

$ret = $mall_order_obj -> ready_pay_order($order_sn,$yue_login_id);

$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);

?>