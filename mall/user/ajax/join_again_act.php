<?php

/**
 * 报名
 * hdw 2015.2.28 将报名支付和继续报名支付合并
 * 极限外拍活动使用
 */

include_once('../common.inc.php');

$event_id			= intval($_INPUT['event_id']);

if(empty($yue_login_id))
{
	$output_arr['code'] = -20;
	$output_arr['message'] = '尚未登录';

	mobile_output($output_arr,false);
	die();
}

$special_event_config = include('/disk/data/htdocs232/poco/pai/config/special_event_config.php');

if($special_event_config[$event_id])
{
	$special_event_arr = explode(',',$special_event_config[$event_id]['yue_user_id']);
	
	if(!in_array($yue_login_id,$special_event_arr))
	{
		$output_arr['code'] = -20;
		$output_arr['message'] = '该活动只能指定用户报名';
	
		mobile_output($output_arr,false);
		die();
	}
}

// 修复1.0.6的bug，接口判断报名人数
$test_enroll_num = intval($_INPUT['table_arr'][0]['enroll_num']);
if($test_enroll_num>3)
{
	$output_arr['code'] = -20;
	$output_arr['message'] = '报名人数不得超过3个';

	mobile_output($output_arr,false);
	die();
}


$has_join = intval($_INPUT['has_join']);

// 不存在enroll_id，是未支付未报名状态
if($has_join == 0)
{
	/*
	* 活动报名
	* 
	* @param array    $data   
	* @param int    $event_id   活动ID
	* @param int    $user_id   报名人ID
	* @param int    $phone   电话号码
	* return bool
	*/


	$phone				= intval($_INPUT['phone']);
	$user_id			= $yue_login_id;
	$enroll_data = array(
	   'user_id'=>$yue_login_id, 
	   'event_id'=>$event_id,
	   'phone'=>$phone,
	   'source'=>'app',
	);
	$sequence_data		= $_INPUT['table_arr'];  //报名场次数组　 
	$user_balance       = $_INPUT['available_balance'];
	$is_available_balance = $_INPUT['is_available_balance'] == 1 ? 0 : 1;// 修正余额支付传递参数，此字段 is_available_balance 为0 是余额支付，1是第三方支付
	$third_code         = trim($_INPUT['third_code']);
	$redirect_url 		= urldecode($_INPUT['redirect_url']);
	$share_event_id     = $_COOKIE['share_event_id'];
    $share_phone        = $_COOKIE['share_phone'];
	$notify_url         = G_MALL_PROJECT_USER_ROOT . "/ajax/pay_activity_notify.php?share_event_id={$share_event_id}&share_phone={$share_phone}";

	//优惠券
	if( is_array($sequence_data[0]) && !empty($sequence_data[0]) && isset($_INPUT['coupon_sn']) )
	{
		$sequence_data[0]['coupon_sn'] = trim($_INPUT['coupon_sn']);
	}

	/**
	 * 约拍提交处理  modify hai 20140911
	 * @param array $enroll_data 
	 * array(
	 *  'user_id'=>'',  用户ID  [非空]
	 *  'event_id'=>,   活动ID  [非空]
	 *  'phone'=>'',    手机号码
	 *  'email'=>'',    邮箱
	 *  'remark'=>      备注
	 * )
	 * @param array $enroll_data  场次数据的二维数组  
	 * array(
	 *  0=>array(
	 *                         
	 *    'enroll_num'=>''  [非空]    报名人数      
	 *    'table_id'=>''    [非空]    场次自增ID 
	 *    'coupon_sn'=>''             优惠码
	 *  
	 *  ),
	 *  1=>array(...
	 * )
	 * @param int    $user_balance 用户余额  用于判断用户是否停留页面太长时间没提交  而用户余额变动后再提交
	 * @param int    $is_available_balance   0为余额支付 1为第三方支付   如果余额不够支付将需要继续跳转到第三方继续支付
	 * @param string $third_code   第三方支付的标识 现暂时支持微信和支付宝钱包 alipay_purse、tenpay_wxapp 当用户使用余额全额支付时可为空
	 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
	 * @param string $notify_url 支付成功后异步的url，为空时使用配置文件中的处理页
	 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
	 * 返回值 status_code 为状态 
	 * status_code错误值 
		 * -1  参数错误
		 * -2  该活动不存在 
		 * -3  活动已结束
		 * -4  参与者为组织者  禁参与
		 * -5  参与人数非法
		 * -6  某一场已经报过名
		 * -7  某一个场次已关闭 不允许再报名
		 * -8  报名产生错误  报名失败
		 * -9  报名已完成并支付  报名失败
		 * -10  用户余额有变动
		 * -11  余额支付失败
		 * -12 跳转到第三方支付产生错误
	 * status_code正确值
	 *   1为提交成功 待组织者审批
	 *   2为余额支付成功   
	 *   3为生成请求参数成功，待跳转到第三方。 
	 * message返回的消息 cur_balance 返回用户当前真实余额[当status_code==2或余额支付成功才有此key] 
	 * request_data 第三方发起请求的字符串[需要发起请求时候才返回]
	 *
	 */
	$ret = add_enroll_op($enroll_data,$sequence_data,$user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url);
	if($ret['status_code'] == 2)
	{
		$ret['status_code'] = 1;
	}
	else if($ret['status_code'] == 3)
	{
		$ret['status_code'] = 2;
	}
}
// 存在enroll_id，是未支付已报名状态， 执行再次支付的方法
else
{
	
	$user_id = $yue_login_id;
	$enroll_data = array(
	   'user_id'=>$yue_login_id, 
	   'event_id'=>$event_id,
	);
	$enroll_id_arr			= $_INPUT['enroll_id_arr'];
	$user_balance			= $_INPUT['available_balance'];
	$is_available_balance	= $_INPUT['is_available_balance'] ;
	$third_code				= trim($_INPUT['third_code']);
	$redirect_url 			= urldecode($_INPUT['redirect_url']);
	$share_event_id     = $_COOKIE['share_event_id'];
    $share_phone        = $_COOKIE['share_phone'];
	$notify_url				= G_MALL_PROJECT_USER_ROOT . "/ajax/pay_activity_notify.php?share_event_id={$share_event_id}&share_phone={$share_phone}";

	//优惠券
	$coupon_sn = trim($_INPUT['coupon_sn']);
	
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
	$ret = again_enroll_op($enroll_data,$enroll_id_arr,$user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url, $coupon_sn);

	
}


/***************统一处理***************/

$output_arr['code'] = $ret['status_code'];
$output_arr['data'] = $ret['request_data'];
$output_arr['payment_no'] = $ret['payment_no'];
$output_arr['channel_return'] = $redirect_url;
$output_arr['message'] = $ret['message'];
$output_arr['cur_balance'] = $ret['cur_balance'];
$output_arr['third_code'] = $third_code;


mall_mobile_output($output_arr,false);

?>