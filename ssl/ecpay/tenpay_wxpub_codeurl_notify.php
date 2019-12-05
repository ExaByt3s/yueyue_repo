<?php
/**
 * 服务器异步通知
 * @author Henry
 */

//引入应用公共文件
require_once ('/disk/data/htdocs232/poco/ecpay/poco_app_common.inc.php');
//记录同步日志
ecpay_log_class::add_log(array(), __FILE__ . '::' . __LINE__, 'tenpay_wxpub_notify');
ob_start();
ignore_user_abort(true);
set_time_limit(0);
//引入应用公共文件
$ecpay_payment_obj  = POCO::singleton('ecpay_payment_class');

$third_code 		= 'tenpay_wxpub_codeurl';

$xml               = $GLOBALS['HTTP_RAW_POST_DATA'];
$array_data 	   = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true); 
$payment_no        = $array_data['out_trade_no'];

$third_class  		= 'ecpay_'. $third_code. '_class';
$third_file   		= $my_app_ecpay->config('INCLUDE_DIR') . '/payment/' . $third_class . '.inc.php';
//引用支付类文件
$payment_info    = $ecpay_payment_obj->get_payment_info($payment_no);
include_once($third_file); 		
$ecpay_third_obj = new $third_class($payment_info);
//检测返回结果
$result 	  = $ecpay_third_obj->check_notify();
$error 		  = 0;
$info 		  = '';
if( $result['error'] === 0 ){

	if( $result['trade_status'] == 'TRADE_FINISHED' || $result['trade_status'] == 'TRADE_SUCCESS'){

		$payment_no = $result['payment_no'];
		$log_id 	= $ecpay_payment_obj->add_log($payment_no, $third_code, 'notify', serialize($result));
		$data 		= $result['data'];
		!is_array($data)&&$data = array();
		$ecpay_payment_obj->update_status_notify($data,$payment_no);
		$payment_info = $ecpay_payment_obj->get_payment_info($payment_no);
		if( !empty( $payment_info ) ){
			//异步通知渠道
			$notify_str = $ecpay_payment_obj->channel_notify($payment_info);
			$notify_str = trim($notify_str);
			$ecpay_payment_obj->update_log(array('channel_result'=>$notify_str), $log_id);
			if($notify_str != 'success') {
				
				$error = 1;
				$info  = 'channel_notify error';

			}

		}
		else{

			$error = 1;
			$info  = ' payment_info is empty';

		}

	}
	elseif($result['trade_status']=='WAIT_BUYER_PAY' ){

	}
	else{

		$error = 1;
		$info  = 'trade_status  is wrong';

	}

}
else{

	$error = 1;
	$info = ' check_notify error';

}
//记录日志
$log_arr = array(

	'third_code' 	=> $third_code,
	'third_class' 	=> $third_class,
	'third_file' 	=> $third_file,
	'result' 		=> $result,
	'payment_info'  => $payment_info,
	'error' 		=> $error,
	'info' 			=> $info

);

if( $error == 0 ){

	$prefix = 'notify';

}
else{

	$prefix = 'notify_error';

}
echo $result['output'];
$output 		   = ob_get_contents();
$log_arr['output'] = $output;
ob_flush();
flush();
ecpay_log_class::add_log($log_arr,$info,$prefix);
?>