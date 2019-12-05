<?php

/**
 * 支付
 * hdw 2014.8.29
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');




$output_arr['data'] = '';
$output_arr['code'] = -4;
$output_arr['message'] = '抱歉，亲，模特邀约功能已暂停使用';

mobile_output($output_arr,false);

exit();
 

$payment_obj = POCO::singleton('pai_payment_class');
$data_log = array(
);
ecpay_log_class::add_log($data_log, 'begin', 'pai_weixin_pay');

$data['from_date_id']   = $yue_login_id; //摄影师ID
$data['to_date_id']     = (int)$_INPUT['model_id'];  //模特ID
$data['date_status']    = 'wait';  //状态
$data['date_time']      = strtotime(trim($_INPUT['date']));  //约拍时间
$data['date_type']      = mb_convert_encoding(trim($_INPUT['type']),'gbk','utf-8'); //拍摄类型
$data['date_style']     = mb_convert_encoding(trim($_INPUT['style']),'gbk','utf-8'); //拍摄风格
$data['date_hour']      = 1;  //拍摄时长
$data['hour']           = $_INPUT['hour'];  //拍摄时长
$data['date_price']     = $_INPUT['price'];  //出价
$data['limit_num']     = (int)$_INPUT['limit_num'];  //人数限制
$data['date_address']   = mb_convert_encoding(trim($_INPUT['address']),'gbk','utf-8'); //地址$data['redirect_url']   = $_INPUT['redirect_url'];
$data['source'] = "weixin";
$data['direct_confirm_id']   =  (int)$_INPUT['direct_confirm_id'];
$third_code = $_INPUT['third_code'];

$available_balance = $_INPUT['available_balance'];
$is_available_balance = $_INPUT['is_available_balance'];
$redirect_url = urldecode($_INPUT['redirect_url']);
$notify_url = G_PAI_APP_DOMAIN . '/m/' . basename(dirname(__FILE__)) . '/pay_date_notify.php';

if(in_array($yue_login_id, unserialize(TEST_USER_ACCOUNT)))
{
	$output_arr['data'] = array();
	$output_arr['code'] = 1;
	$output_arr['message'] = 'test pay ok';

	mobile_output($output_arr,false);

	exit();
}



/**
 * 约拍提交处理  modify hai 20140911
 * @param array $date_data 
 * array( 
 *	'from_date_id'=>'', //摄影师ID 调用时以$yue_login_id 赋值
 *	'to_date_id'=>'',   //模特ID
 *	'date_status'=>'',  //状态     传入wait
 *	'date_time'=>'',    //约拍时间
 *	'date_type'=>'',    //拍摄类型
 *	'date_style'=>'',   //拍摄风格
 *	'date_hour'=>'',    //拍摄时长
 *	'date_price'=>'',   //出价
 *	'date_address'=>''  //地址
 *
 *)
 * @param int    0为全额支付   1为余额支付   如果余额不够支付将需要继续跳转到第三方继续支付 
 * @param int    $user_balance 用户余额      用于判断用户是否停留页面太长时间没提交  而用户余额变动后再提交
 * @param string $third_code   第三方支付的标识 现暂时支持微信和支付宝钱包 alipay_purse、tenpay_wxapp 当用户使用余额全额支付时可为空
 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * 返回值 status_code 为是否错误  status_code -1 参数错误 -2用户余额有变动   -3添加到约拍表时产生错误  -4为生成第三方请求参数产生错误。
 * 1为余额支付成功   2为生成请求参数成功，待跳转到第三方。
 * message返回的消息 cur_balance 返回用户当前真实余额[当status_code==2才有此key] request_data 第三方发起请求的字符串[需要发起请求时候才返回]
 *
 */
$ret = add_event_date_op_v2($data,$available_balance,$is_available_balance,$third_code,$redirect_url, $notify_url);
$channel_return = $redirect_url;
if( !empty($ret['payment_no']) && strpos($channel_return, '#')!==false )
{
	//兼容约拍的JS结构需求
	$channel_return .= '/';
	$channel_return .= "payment_no_{$ret['payment_no']}";
}

//获取微信JSSDK签名数据
$app_id = 'wx25fbf6e62a52d11e';	//约约正式号
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $_GET['url']);

$output_arr['third_code']   = $third_code;
$output_arr['data'] 		= $ret['request_data'];
$output_arr['payment_no'] 	= $ret['payment_no'];
$output_arr['channel_return'] = $channel_return;
$output_arr['code'] 		= $ret['status_code'];
$output_arr['message'] 		= $ret['message'];
$output_arr['cur_balance']  = $ret['cur_balance'];
$output_arr['wx_sign_package'] = $wx_sign_package;

$payment_obj = POCO::singleton('pai_payment_class');
$data_log = array(
	'cookie' => $_COOKIE,
	'input' => $_INPUT,
	'output_arr' => $output_arr,
);
ecpay_log_class::add_log($data_log, 'data_log', 'pai_weixin_pay');

mobile_output($output_arr,false);

?>