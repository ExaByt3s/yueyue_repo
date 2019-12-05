<?php
/*
 * 2014-07-16
 * 支付宝钱包支付完成后验证签名页
 * hai
*/
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
function output_json($info_arr){

	ecpay_log_class::add_log($info_arr, 'alipay_purse_verify_RETURN', 'alipay_purse_verify_RETURN');
	$info_arr['msg'] = iconv('GBK','UTF-8',$info_arr['msg']);
	echo json_encode($info_arr);
	exit();

}
//引用  用于构造函数初始化支付系统为正式版或测试版 
if( defined('G_PAI_ECPAY_DEV') ){
//测试模式
	$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dev_dir');
	include_once $ecpay_app_dir . '/poco_app_common.inc.php';

}
else{
	
	$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dir');
	include_once $ecpay_app_dir . '/poco_app_common.inc.php';

}
include_once( $ecpay_app_dir.'/include/payment/ecpay_alipay_purse_class.inc.php' );  
$ecpay_payment_obj 		= POCO::singleton('ecpay_payment_class');
$return_data 			= $_INPUT['return_data'];			 //软件那边传过来的支付宝返回信息
$sys 					= $_INPUT['sys'];					 //手机系统
$wap_pay 				= intval($_INPUT['wap_pay']);					   
$return_data 		 	= str_replace(' ','+',$return_data);   //base64后可能会有加号，接收到含加号的值会变成空值；所以需要替换。
$return_data 		 	= base64_decode($return_data);

$log_arr = array(

    'request' => $_REQUEST,
	'HTTP_GET_VARS' => $HTTP_GET_VARS,
	'ibforums_input' => $ibforums->input,
    'input' => $_INPUT, 
    'replaced_return_data' => str_replace(' ','+',$_INPUT['return_data']), 
    'decoded_return_data' => base64_decode(str_replace(' ','+',$_INPUT['return_data'])), 

    'third_code'   => 'alipay_purse',
    'wap_pay' 	   => $wap_pay,
    'return_data'  => iconv( 'UTF-8','GBK',$return_data ),

);
ecpay_log_class::add_log($log_arr, 'alipay_purse_verify_NEW', 'alipay_purse_verify_info_NEW');
if( !in_array($sys,array('android','ios') ) ){

	$output = array('status'=>"0",'msg'=>'验证系统标识错误');
	output_json($output);

}
if( $sys == 'android' || $wap_pay == 1 ){
	//点击支付宝钱包支付   但用户没安装支付宝钱包的时候；跳转到支付宝wap支付。由于苹果wap返回的参数和客户端返回的不一样
	//但和安卓返回的一致，所以以安卓的处理方式进行处理。
	//安卓与苹果app 返回的数据格式不一样。所以需要分开进行处理。
	/*******
	* 安卓传递的格式
	* resultStatus={9000};memo={};result={partner="2088201403648682"&seller_id="huangyl@poco.cn"&service="mobile.securitypay.pay"&
	* notify_url="http%3A%2F%2Fwww1.poco.cn%2Fpaytest%2Fpayment%2Falipay_purse_notify.php"&_input_charset="utf-8"&payment_type="1"&
	* it_b_pay="15m"&subject="商品测试[安卓]"&body="商品描述"&out_trade_no="P2565752241"&total_fee="0.01"&success="true"&sign_type="RSA"
	* &sign="XHiUKdUqL9m0Vt5PO+V+UhaWF7jolP88/t91oTsgRe7Z6lakW3VNX4yTfXKesQtBpTegWGFIPVZ9nyU/h9vxY+OOQt1Da7UYXOtI+ReZk304KB7IsTwVbwzXfjLbJ6Br7FQFJ0XBnKbVX4r3HHZQf5OCKGIoS3BfSm7BKmHZMqs="}
	*******/
	$result_str 		 = getSignStr($return_data);  			//需要签名的字符串
	$order_id    		 = getOrder($result_str);
	$sign 	   			 = getSign($return_data);     			//签名的数据
	$payment_info 			= $ecpay_payment_obj->get_payment_info($order_id);
	$ecpay_alipay_purse_obj = POCO::singleton('ecpay_alipay_purse_class',array($payment_info) );
	$ali_public_key_path = $ecpay_alipay_purse_obj->get_ali_public_key_path();
	$sign_result 		 = rsaVerify($result_str, $ali_public_key_path,$sign);
	$result_status 	 	 = substr_count($return_data,'resultStatus={9000}');
	$success 	  	 	 = substr_count($return_data,'success="true"');


}
elseif( $sys == 'ios' ){
	/*******
	* 苹果传递的格式
	* {"memo":{"result":"partner=\"2088201403648682\"&seller_id=\"huangyl@poco.cn\"&service=\"mobile.securitypay.pay\"&notify_url=\"
	* http%3A%2F%2Fwww1.poco.cn%2Fpaytest%2Fpayment%2Falipay_purse_notify.php\"&_input_charset=\"utf-8\"&payment_type=\"1\"&it_b_pay=\"15m\"
	* &subject=\"商品测试[苹果]\"&body=\"商品描述\"&out_trade_no=\"P2565753639\"&total_fee=\"0.01\"&success=\"true\"&sign_type=\"RSA\"&
	* sign=\"D11rCp3+FrpcBDaUnc1mjhJuyGNzvfGyELYR3jXIp3GC/UtgZeezMTClu/X0q6xC4/oKP2kcaOF8bGbrQfDwXQvF4pl4THUvTIFmmpD6aBLcopuurd/CjQcwHvithP0+cU3DG8TSRBS3irLcrLFnxJ1Dd3kpwkdCUFXWleuHm6c=\"",
	* "memo":"","ResultStatus":"9000"},"requestType":"SafePay"}
	*
	*/
	$__data 	   = json_decode($return_data,true);
	$result  	   = $__data['memo']['result'];
	$resultStatus  = $__data['memo']['ResultStatus'];
	unset($__data);
	$result_str    = getSignStr($result);
	$order_id      = getOrder($result_str);
	$sign 		   = getSign( $result );
	$payment_info = $ecpay_payment_obj->get_payment_info($order_id);
	$ecpay_alipay_purse_obj = POCO::singleton('ecpay_alipay_purse_class',array($payment_info) );
	$ali_public_key_path 	= $ecpay_alipay_purse_obj->get_ali_public_key_path();
	$sign_result   = rsaVerify($result_str,$ali_public_key_path,$sign);
	$resultStatus == 9000 ?$result_status = 1:$result_status = 0;
	$success 	   = substr_count($result, 'success="true"');
	

}
if( !$sign_result ){

	$output = array('status'=>"0",'msg'=>'签名验证失败');
	output_json($output);

}

if ( $result_status!= 1 && $success != 1 ){

	$output = array('status'=>"0",'msg'=>'支付失败');
	output_json($output);

}
//获取支付信息
if( empty($payment_info) )
{
	$output = array('status'=>"0",'msg'=>'非法支付号');
	output_json($output);

}
//TODO 临时处理，为了解决用户在支付宝付了钱，但异步通知却还没到达。
if( $payment_info['channel_code']=='pai' && $payment_info['channel_module']=='recharge' )
{
	$pai_recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
	$recharge_info = $pai_recharge_obj->get_recharge_info($payment_info['channel_rid']);
	
	if( $recharge_info['user_id']>0 && $recharge_info['date_id']>0 )
	{
//		include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
//		send_message_for_10002($recharge_info['user_id'], "您已成功提交约拍申请，付款状态查询中。付款成功后，可在【我的】-【约拍】栏目查看详情");
	}

}
if( $payment_info['status'] == 0 ){
	//到支付宝钱包那边再查查订单状态 有可能是
	//$order_info 		= $tenpay_wxapp_obj->get_order_info($order_id);
	if( !empty($order_info) ){

		if( $order_info->ret_code === 0  && $order_info->trade_state === "0" ){
			//返回状态：1表示已支付、-1表示等待确认，0与其它表示错误
			//微信查询回的结果为已支付  接着更新我们这边的订单状态
			$output = array('status'=>"1",'msg'=>'支付成功!','payment_no'=>$order_id);
			output_json($output);

		}
		else{
			//因为订单查询结果错误，出错了。 
			$output = array('status'=>"0",'msg'=>'微信订单查询错误，找不到相应的数据。','payment_no'=>$order_id) ;
			output_json($output);

		}

	}
	else{
		//查询不到结果  可能是微信网络延时。
		$output = array('status'=>"-1",'msg'=>'等待确认','payment_no'=>$order_id) ;				
		output_json($output);

	}

}
else if( $payment_info['status'] == 8 ){

	$output = array('status'=>"1",'msg'=>'支付成功','payment_no'=>$order_id) ;
	output_json($output);

}

?>
