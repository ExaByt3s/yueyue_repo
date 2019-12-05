<?php
/**
 * @Author      : hudw <hudw@poco.cn>
 * @Date        : 2015-10-21
 * @Description : 商城支付页面计算金额
 */

include_once 'config.php';

// ========== 接受参数 ==========

// 订单号
$order_sn = trim($_INPUT['order_sn']);
// 用户id
$user_id = $yue_login_id;
// 是否使用余额
$is_available_balance = trim($_INPUT['is_available_balance']);
// 优惠劵码
$coupon_sn = trim($_INPUT['coupon_sn']);

$order_obj  = POCO::singleton('pai_mall_order_class');

// ========== 接受参数 ==========
 
// 计算
$ret = $order_obj->cal_pay_order($order_sn,$user_id,$is_available_balance,$coupon_sn);

if(!$ret['result']['is_allow_coupon'])
{
	$ret['result']['no_allow_coupon_text'] = '很抱歉！该促销暂未支持优惠劵使用';
}

$output_arr['msg'] = $ret['message'];
$output_arr['code'] = $ret['result'];
$output_arr['data'] = $ret['response_data'];

mall_mobile_output($output_arr,false);
?>