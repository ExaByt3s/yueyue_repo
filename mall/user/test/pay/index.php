<?php
include_once 'config.php';

$pc_wap = 'wap/';

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if($check_arr['switch'] == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	header("Location:{$url}");
	die();
}

$order_sn = trim($_INPUT['order_sn']);

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'pay/index.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();

// 当前支付页面回链
$cur_page_url = urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);

// ============ 从这里开始，是初始化支付页面的数据 ============

// 订单类：pai_mall_order_class
$mall_order_obj = POCO::singleton('pai_mall_order_class');

/**
* 获取完整信息
* @param string $order_sn
* @return array
*/
// 13241194
$order_full_info = $mall_order_obj->get_order_full_info($order_sn);
if( empty($order_full_info) || $order_full_info['buyer_user_id']!=$yue_login_id )
{
	//此订单不属于这位用户
	die('This order does not belong to you.');
}

//订单类型
$order_type = $order_full_info['order_type'];

//默认选中最好的优惠券
$coupon_sn = trim($order_full_info['coupon_sn']);
if( strlen($coupon_sn)<1 )
{
	$param_info = $mall_order_obj->get_coupon_param_info_by_order_sn($order_sn, $yue_login_id);
	if( !empty($param_info) )
	{
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$coupon_info_best = $coupon_obj->get_user_coupon_info_best($yue_login_id, 1, $param_info);
		$coupon_sn = trim($coupon_info_best['coupon_sn']);
	}
}

//促销信息
$goods_promotion_info = $order_full_info['goods_promotion_info'];
if( !empty($goods_promotion_info) )
{
	$detail_info['goods_promotion_info'] = '<span style="color:#fe9220;" class="ml5">'.$goods_promotion_info['type_name'].
'</span><span class="ml15">立省：<label style="color:#fe9220;">'.$goods_promotion_info['cal_used_amount'].'</label></span>';

}

//初始化支付金钱
$cal_pay_ret = $mall_order_obj->cal_pay_order($order_sn, $yue_login_id, 1, $coupon_sn);
$cal_pay_arr = $cal_pay_ret['response_data'];


/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;

$detail_info['is_weixin'] = $__is_weixin;
$detail_info['is_yueyue_app'] = $__is_yueyue_app;

if(!$__is_yueyue_app && !$__is_weixin )
{
	$detail_info['is_weixin'] = false;
	$detail_info['is_yueyue_app'] = false;
	$detail_info['is_zfb_wap'] = true;
}
if($order_type == 'activity')
{
	// 活动订单详情列表
	$detail_info['activity_list'] = $order_full_info['activity_list'];
}
elseif ($order_type == 'detail')
{
	// 商城订单详情列表
	$detail_info['detail_list'] = $order_full_info['detail_list'];
}
else
{
	// 马上支付详情列表
	$detail_info['payment_list'][] = array(
		'seller_user_id' => 'ID：'.$order_full_info['seller_user_id'],
		'seller_icon' => $order_full_info['seller_icon'],
		'seller_name' => $order_full_info['seller_name']
	);
}

if($_INPUT['db'] == 1)
{
	print_r($order_full_info);
}


// 用户ID
$detail_info['user_id'] = $yue_login_id;
// 用户余额
$detail_info['available_balance'] = $cal_pay_arr['available_balance']; //$ret['available_balance'];
// 支付总价
$detail_info['total_amount'] = $cal_pay_arr['total_amount'];
// 是否使用优惠劵
$detail_info['is_allow_coupon'] = $cal_pay_arr['is_allow_coupon'];
// 优惠劵名称
$detail_info['batch_name'] = $cal_pay_arr['batch_name'];
// 优惠劵面值
$detail_info['coupon_amount'] = $cal_pay_arr['coupon_amount'];
// 支付的钱
$detail_info['use_balance'] = $cal_pay_arr['use_balance'];
// 支付总价
$detail_info['pending_amount'] = $cal_pay_arr['pending_amount'];
$detail_info['is_available_balance'] = $cal_pay_arr['is_available_balance'];
$detail_info['is_use_third_party_payment'] = $cal_pay_arr['is_use_third_party_payment'];

if(!$detail_info['is_allow_coupon'])
{
	$detail_info['no_allow_coupon_text'] = '很抱歉！该促销暂未支持优惠劵使用';
}

$output_arr['data'] = $detail_info;

// ============ 从这里结束，是初始化支付页面的数据 ============

$tpl->assign('cur_page_url', $cur_page_url);
$tpl->assign('wap_global_footer', $wap_global_footer);
$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('order_sn',$order_sn);
$tpl->assign('page_data',mall_output_format_data($output_arr));
$tpl->output();
?>