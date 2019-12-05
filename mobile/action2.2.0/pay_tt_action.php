<?php

/**
 * tt支付
 * hdw 2015.4.15
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$quotes_id = intval($_INPUT['quotes_id']);
$third_code = trim($_INPUT['third_code']);
$coupon_sn = trim($_INPUT['coupon_sn']);
$available_balance = trim($_INPUT['available_balance']);
$is_available_balance = trim($_INPUT['is_available_balance']);
$redirect_url = trim($_INPUT['redirect_url']);
$notify_url = G_PAI_APP_DOMAIN . '/mobile/' . basename(dirname(__FILE__)) . '/pay_quotes_notify.php';

//获取报价信息
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
$request_id = intval($quotes_info['request_id']);

//获取需求信息
$task_request_obj = POCO::singleton('pai_task_request_class');
$request_info = $task_request_obj->get_request_info($request_id);
$buyer_user_id = intval($request_info['user_id']);

if( $yue_login_id>0 && $yue_login_id==$buyer_user_id )
{
	/**
	 * 提交支付
	 * @param int $quotes_id
	 * @param double $available_balance 页面当前余额
	 * @param int $is_available_balance 是否使用余额，0否 1是
	 * @param string $third_code 支付方式 alipay_purse tenpay_wxapp，当用户使用余额全额支付时可为空
	 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
	 * @param string $notify_url
	 * @param string $coupon_sn
	 * @return array result 1调取第三方支付，2余额支付成功
	 */
	$ret = $task_quotes_obj->submit_pay_quotes_v2($quotes_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn);
}
else
{
	$ret = array('result'=>-1, 'message'=>'参数错误', 'payment_no'=>'', 'request_data'=>'');
}

$ret['third_code'] = $third_code;

//日志
pai_log_class::add_log($ret, 'pay end', 'pay_tt');

mobile_output($ret,false);

?>