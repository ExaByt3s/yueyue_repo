<?php
/*
 * 2014-12-03
 * 微信支付返回验证页
 * hai
 */
//支付系统测试平台
//define('G_PAI_ECPAY_DEV', 1);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
if( defined('G_PAI_ECPAY_DEV') ){
	//测试模式
	$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dev_dir');
	include_once $ecpay_app_dir . '/poco_app_common.inc.php';

}
else{
	
	$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dir');
	include_once $ecpay_app_dir . '/poco_app_common.inc.php';

}
include_once( $ecpay_app_dir.'/include/payment/ecpay_tenpay_wxapp_class.inc.php' );
$ecpay_payment_obj 	= POCO::singleton('ecpay_payment_class');
$payment_no 		= $_INPUT['payment_no'];
$payment_info 		= $ecpay_payment_obj->get_payment_info($payment_no);
if( empty($payment_info) ){
	
	$info =  array('status'=>"0",'msg'=>iconv('GBK','UTF-8','非法订单参数'),'payment_no'=>$payment_no ) ;
	ecpay_log_class::add_log($info, 'tenpay_wxapp_verify_test', 'tenpay_wxapp_verify_info_test');
	echo json_encode($info);
	die();

}
if( $payment_info['status'] == 0 ){
	
	$tenpay_wxapp_obj 	= POCO::singleton('ecpay_tenpay_wxapp_class',array($payment_info));
	//到微信那边再查查订单状态 有可能是
	$order_info 		= $tenpay_wxapp_obj->get_order_info($payment_no);
	
	if( !empty($order_info) ){

		if( $order_info->ret_code === 0  && $order_info->trade_state === "0" ){
			//返回状态：1表示已支付、-1表示等待确认，0与其它表示错误
			//微信查询回的结果为已支付  接着更新我们这边的订单状态
			$info = array('status'=>"1",'msg'=>'支付成功!','payment_no'=>$payment_no) ;

		}
		else{
			//因为订单查询结果错误，出错了。 
			$info = array('status'=>"0",'msg'=>'微信订单查询错误，找不到相应的数据。','payment_no'=>$payment_no) ;

		}

	}
	else{
		//查询不到结果  可能是微信网络延时。
		$info = array('status'=>"-1",'msg'=>'等待确认','payment_no'=>$payment_no) ;				

	}

}
else if( $payment_info['status'] == 8 ){

	$info = array('status'=>"1",'msg'=>'支付成功','payment_no'=>$payment_no) ;

}
$info['msg'] = iconv('GBK','UTF-8',$info['msg']);
echo json_encode($info);
ecpay_log_class::add_log($info, 'tenpay_wxapp_verify_test', 'tenpay_wxapp_verify_info_test');

?>
