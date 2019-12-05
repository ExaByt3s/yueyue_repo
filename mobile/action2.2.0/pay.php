<?php

/**
 * 支付
 * hdw 2014.8.29
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$date_id = $_INPUT['date_id'];
$third_code = $_INPUT['third_code'];

$coupon_sn = $_INPUT['coupon_sn'];

$available_balance = $_INPUT['available_balance'];
$is_available_balance = $_INPUT['is_available_balance'];
$redirect_url = urldecode($_INPUT['redirect_url']);
$notify_url = G_PAI_APP_DOMAIN . '/mobile/' . basename(dirname(__FILE__)) . '/pay_date_notify.php';



if(in_array($yue_login_id, unserialize(TEST_USER_ACCOUNT)))
{
	$output_arr['data'] = array();
	$output_arr['code'] = 1;
	$output_arr['message'] = 'test pay ok';

	mobile_output($output_arr,false);

	exit();
}

$check_ua=preg_match('#(yue_pai/3\.0\.10|yue_pai/3\.0\.0|yue_pai 3\.0\.0|yue_pai 3\.0\.10)#',$_SERVER['HTTP_USER_AGENT']);
if($check_ua)
{
    $output_arr['code'] = 0;
    $output_arr['message'] = "抱歉，亲，模特邀约功能已暂停使用";
    mobile_output($output_arr,false);
    exit();
}

/**
 * 约拍提交处理
 * @param int $date_id 
 * @param int    0为全额支付   1为余额支付   如果余额不够支付将需要继续跳转到第三方继续支付 
 * @param int    $available_balance 用户余额      用于判断用户是否停留页面太长时间没提交  而用户余额变动后再提交
 * @param string $third_code   第三方支付的标识 现暂时支持微信和支付宝钱包 alipay_purse、tenpay_wxapp 当用户使用余额全额支付时可为空
 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
 * @param string $notify_url
 * @param string $coupon_sn
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * 返回值 status_code 为是否错误  status_code -1 参数错误 -2用户余额有变动   -3添加到约拍表时产生错误  -4为生成第三方请求参数产生错误。
 * 1为余额支付成功   2为生成请求参数成功，待跳转到第三方。
 * message返回的消息 cur_balance 返回用户当前真实余额[当status_code==2才有此key] request_data 第三方发起请求的字符串[需要发起请求时候才返回]
 *
 */

$ret = update_date_op($date_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url = '',$coupon_sn);

$channel_return = $redirect_url;
if( !empty($ret['payment_no']) && strpos($channel_return, '#')!==false )
{
	//兼容约拍的JS结构需求
	$channel_return .= '/';
	$channel_return .= "payment_no_{$ret['payment_no']}";
}

$output_arr['third_code']   = $third_code;
$output_arr['data'] 		= $ret['request_data'];
$output_arr['payment_no'] 	= $ret['payment_no'];
$output_arr['channel_return'] = $channel_return;
$output_arr['code'] 		= $ret['status_code'];
$output_arr['message'] 		= $ret['message'];
$output_arr['cur_balance']  = $ret['cur_balance'];

//日志
pai_log_class::add_log(array('ret'=>$ret,'output_arr'=>$output_arr), 'pay end', 'pay');

mobile_output($output_arr,false);

?>