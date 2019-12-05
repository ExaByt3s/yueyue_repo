<?php

/**
 * 报名
 * hdw 2014.9.4
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}

/*
* 活动报名
* 
* @param array    $data   
* @param int    $event_id   活动ID
* @param int    $user_id   报名人ID
* @param int    $phone   电话号码
* return bool
*/


$event_id			= intval($_INPUT['event_id']);
$phone				= intval($_INPUT['phone']);
$user_id			= $yue_login_id;
$enroll_data = array(
   'user_id'=>$yue_login_id, 
   'event_id'=>$event_id,
   'phone'=>$phone
);
$sequence_data		= $_INPUT['table_arr'];  //报名场次数组　 
$user_balance       = $_INPUT['available_balance'];
$is_available_balance = $_INPUT['is_available_balance'] == 'true' ? 0 : 1;
$third_code         = trim($_INPUT['third_code']);
$redirect_url 		= urldecode($_INPUT['redirect_url']);
$notify_url         = G_PAI_APP_DOMAIN . '/m/' . basename(dirname(__FILE__)) . '/pay_activity_notify.php';

/**
 * 约拍报名，继续支付
 * @param array $enroll_data
 * array(
 *  'user_id'=>'',  用户ID  [非空]
 *  'event_id'=>,   活动ID  [非空]
 * )
 * @param array $enroll_id_arr  报名ID
 * array(
 *  1,2
 * )
 * @param int    $user_balance 用户余额  用于判断用户是否停留页面太长时间没提交  而用户余额变动后再提交
 * @param int    $is_available_balance   0为余额支付 1为第三方支付   如果余额不够支付将需要继续跳转到第三方继续支付
 * @param string $third_code   第三方支付的标识 现暂时支持微信和支付宝钱包 alipay_purse、tenpay_wxapp 当用户使用余额全额支付时可为空
 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * 返回值 status_code 为状态
 * status_code错误值
 * -1  参数错误
 * -2  该活动不存在
 * -3  活动已结束
 * -10 用户余额有变动
 * -11 余额支付失败
 * -12 跳转到第三方支付产生错误
 * status_code正确值
 *   1为余额支付成功
 *   2为生成请求参数成功，待跳转到第三方。
 * message返回的消息 cur_balance 返回用户当前真实余额[当status_code==2或余额支付成功才有此key]
 * request_data 第三方发起请求的字符串[需要发起请求时候才返回]
 *
 */


$ret = add_enroll_op($enroll_data,$sequence_data,$user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url);


//$ret = add_enroll_op_v2($data, $event_id,$user_id,$phone);

$channel_return = $redirect_url;
if( !empty($ret['payment_no']) && strpos($channel_return, '#')!==false )
{
	//兼容约拍的JS结构需求
	$channel_return .= '/';
	$channel_return .= "payment_no_{$ret['payment_no']}";
}

$output_arr['code'] = $ret['status_code'];
$output_arr['data'] = $ret['request_data'];
$output_arr['payment_no'] = $ret['payment_no'];
$output_arr['channel_return'] = $channel_return;
$output_arr['message'] = $ret['message'];
$output_arr['user_info'] = $ret['user_info'];
$output_arr['third_code'] = $third_code;

mobile_output($output_arr,false);

?>
