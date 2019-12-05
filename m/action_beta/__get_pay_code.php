<?php
	include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
	$pai_payment_obj 	= POCO::singleton('pai_payment_class');
	$payment_obj		= POCO::singleton('ecpay_payment_class');
	
	if( $_INPUT['third_code'] == 'alipay_purse' ){

		preg_match('/iphone/i',$_SERVER['HTTP_USER_AGENT'])?$subject = '��Ʒ����[ƻ��]':$subject = '��Ʒ����[��׿]';

		$ret         = $payment_obj->submit_payment(
		    'pai',
		    'recharge',
		    100000,
		    array('third_code'=>'alipay_purse','subject'=>$subject,'body'=>'��Ʒ����','amount'=>0.01,'channel_return'=>'http://baidu.com')
		);
		$request_str				= $ret['request_data'];
		$output_arr['payment_no']   = $ret['payment_no'];
		$output_arr['data']			= $request_str;
		echo json_encode($output_arr);
	
	}
	else if( $_INPUT['third_code'] == 'tenpay_wxapp' ){

		preg_match('/iphone/i',$_SERVER['HTTP_USER_AGENT'])?$subject = '΢��֧������[ƻ��]':$subject = '΢��֧������[��׿]';
		$subject    .= substr(time(),5);
		$ret         = $payment_obj->submit_payment(

		    'pai',
		    'recharge',
		    100000,
		    array('third_code'=>'tenpay_wxapp','subject'=>$subject,'amount'=>0.01)

		);
		echo($ret['request_data']);
		
	}

?>