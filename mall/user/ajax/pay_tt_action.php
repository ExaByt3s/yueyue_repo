<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 8 July, 2015
 * @package default
 */

/**
 * Define 支付页
 */

include_once('../common.inc.php');

// 权限检查
mall_check_user_permissions($yue_login_id);

// 订单类
$mall_order_obj = POCO::singleton('pai_mall_order_class');

$order_sn = trim($_INPUT['order_sn']);
$third_code = trim($_INPUT['third_code']);
$coupon_sn = trim($_INPUT['coupon_sn']);
$available_balance = trim($_INPUT['available_balance']);
$is_available_balance = trim($_INPUT['is_available_balance']);
$redirect_url = trim($_INPUT['redirect_url']);
$notify_url =  G_MALL_PROJECT_USER_ROOT . '/ajax/pay_order_notify.php';
$user_id = $yue_login_id;
$more_info = array('page_total_amount'=>trim($_INPUT['total_amount']));


//获取报价信息

/**
* 获取完整信息
* @param string $order_sn
* @return array
*/	 
$order_full_info = $mall_order_obj->get_order_full_info($order_sn);

$buyer_user_id = intval($order_full_info['buyer_user_id']);

if( $yue_login_id>0 && $yue_login_id==$buyer_user_id )
{
/**
   * 提交支付
   * @param string $order_sn
   * @param int $user_id 买家用户ID
   * @param double $available_balance 页面当前余额
   * @param int $is_available_balance 是否使用余额，0否 1是
   * @param string $third_code 支付方式 alipay_purse tenpay_wxapp，当用户使用余额全额支付时可为空
   * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
   * @param string $notify_url
   * @return array array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'')
   * result 1调取第三方支付，2余额支付成功
   */
   $ret = $mall_order_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn,$more_info);
}
else
{
	$ret = array('result'=>-1, 'message'=>'参数错误', 'payment_no'=>'', 'request_data'=>'');
}

$ret['third_code'] = $third_code;
$ret['order_sn'] = $order_sn;

//日志
pai_log_class::add_log($ret, 'pay end', 'pay_tt');

mall_mobile_output($ret,false);

?>
