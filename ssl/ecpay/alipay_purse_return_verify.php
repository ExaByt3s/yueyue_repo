<?php
/*
 * 2014-07-16
 * ֧����Ǯ��֧����ɺ���֤ǩ��ҳ
 * hai
*/
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
function output_json($info_arr){

	ecpay_log_class::add_log($info_arr, 'alipay_purse_verify_RETURN', 'alipay_purse_verify_RETURN');
	$info_arr['msg'] = iconv('GBK','UTF-8',$info_arr['msg']);
	echo json_encode($info_arr);
	exit();

}
//����  ���ڹ��캯����ʼ��֧��ϵͳΪ��ʽ�����԰� 
if( defined('G_PAI_ECPAY_DEV') ){
//����ģʽ
	$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dev_dir');
	include_once $ecpay_app_dir . '/poco_app_common.inc.php';

}
else{
	
	$ecpay_app_dir = POCO_APP_PAI::ini('payment/ecpay_app_dir');
	include_once $ecpay_app_dir . '/poco_app_common.inc.php';

}
include_once( $ecpay_app_dir.'/include/payment/ecpay_alipay_purse_class.inc.php' );  
$ecpay_payment_obj 		= POCO::singleton('ecpay_payment_class');
$return_data 			= $_INPUT['return_data'];			 //����Ǳߴ�������֧����������Ϣ
$sys 					= $_INPUT['sys'];					 //�ֻ�ϵͳ
$wap_pay 				= intval($_INPUT['wap_pay']);					   
$return_data 		 	= str_replace(' ','+',$return_data);   //base64����ܻ��мӺţ����յ����Ӻŵ�ֵ���ɿ�ֵ��������Ҫ�滻��
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

	$output = array('status'=>"0",'msg'=>'��֤ϵͳ��ʶ����');
	output_json($output);

}
if( $sys == 'android' || $wap_pay == 1 ){
	//���֧����Ǯ��֧��   ���û�û��װ֧����Ǯ����ʱ����ת��֧����wap֧��������ƻ��wap���صĲ����Ϳͻ��˷��صĲ�һ��
	//���Ͱ�׿���ص�һ�£������԰�׿�Ĵ���ʽ���д���
	//��׿��ƻ��app ���ص����ݸ�ʽ��һ����������Ҫ�ֿ����д���
	/*******
	* ��׿���ݵĸ�ʽ
	* resultStatus={9000};memo={};result={partner="2088201403648682"&seller_id="huangyl@poco.cn"&service="mobile.securitypay.pay"&
	* notify_url="http%3A%2F%2Fwww1.poco.cn%2Fpaytest%2Fpayment%2Falipay_purse_notify.php"&_input_charset="utf-8"&payment_type="1"&
	* it_b_pay="15m"&subject="��Ʒ����[��׿]"&body="��Ʒ����"&out_trade_no="P2565752241"&total_fee="0.01"&success="true"&sign_type="RSA"
	* &sign="XHiUKdUqL9m0Vt5PO+V+UhaWF7jolP88/t91oTsgRe7Z6lakW3VNX4yTfXKesQtBpTegWGFIPVZ9nyU/h9vxY+OOQt1Da7UYXOtI+ReZk304KB7IsTwVbwzXfjLbJ6Br7FQFJ0XBnKbVX4r3HHZQf5OCKGIoS3BfSm7BKmHZMqs="}
	*******/
	$result_str 		 = getSignStr($return_data);  			//��Ҫǩ�����ַ���
	$order_id    		 = getOrder($result_str);
	$sign 	   			 = getSign($return_data);     			//ǩ��������
	$payment_info 			= $ecpay_payment_obj->get_payment_info($order_id);
	$ecpay_alipay_purse_obj = POCO::singleton('ecpay_alipay_purse_class',array($payment_info) );
	$ali_public_key_path = $ecpay_alipay_purse_obj->get_ali_public_key_path();
	$sign_result 		 = rsaVerify($result_str, $ali_public_key_path,$sign);
	$result_status 	 	 = substr_count($return_data,'resultStatus={9000}');
	$success 	  	 	 = substr_count($return_data,'success="true"');


}
elseif( $sys == 'ios' ){
	/*******
	* ƻ�����ݵĸ�ʽ
	* {"memo":{"result":"partner=\"2088201403648682\"&seller_id=\"huangyl@poco.cn\"&service=\"mobile.securitypay.pay\"&notify_url=\"
	* http%3A%2F%2Fwww1.poco.cn%2Fpaytest%2Fpayment%2Falipay_purse_notify.php\"&_input_charset=\"utf-8\"&payment_type=\"1\"&it_b_pay=\"15m\"
	* &subject=\"��Ʒ����[ƻ��]\"&body=\"��Ʒ����\"&out_trade_no=\"P2565753639\"&total_fee=\"0.01\"&success=\"true\"&sign_type=\"RSA\"&
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

	$output = array('status'=>"0",'msg'=>'ǩ����֤ʧ��');
	output_json($output);

}

if ( $result_status!= 1 && $success != 1 ){

	$output = array('status'=>"0",'msg'=>'֧��ʧ��');
	output_json($output);

}
//��ȡ֧����Ϣ
if( empty($payment_info) )
{
	$output = array('status'=>"0",'msg'=>'�Ƿ�֧����');
	output_json($output);

}
//TODO ��ʱ����Ϊ�˽���û���֧��������Ǯ�����첽֪ͨȴ��û���
if( $payment_info['channel_code']=='pai' && $payment_info['channel_module']=='recharge' )
{
	$pai_recharge_obj = POCO::singleton('ecpay_pai_recharge_class');
	$recharge_info = $pai_recharge_obj->get_recharge_info($payment_info['channel_rid']);
	
	if( $recharge_info['user_id']>0 && $recharge_info['date_id']>0 )
	{
//		include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
//		send_message_for_10002($recharge_info['user_id'], "���ѳɹ��ύԼ�����룬����״̬��ѯ�С�����ɹ��󣬿��ڡ��ҵġ�-��Լ�ġ���Ŀ�鿴����");
	}

}
if( $payment_info['status'] == 0 ){
	//��֧����Ǯ���Ǳ��ٲ�鶩��״̬ �п�����
	//$order_info 		= $tenpay_wxapp_obj->get_order_info($order_id);
	if( !empty($order_info) ){

		if( $order_info->ret_code === 0  && $order_info->trade_state === "0" ){
			//����״̬��1��ʾ��֧����-1��ʾ�ȴ�ȷ�ϣ�0��������ʾ����
			//΢�Ų�ѯ�صĽ��Ϊ��֧��  ���Ÿ���������ߵĶ���״̬
			$output = array('status'=>"1",'msg'=>'֧���ɹ�!','payment_no'=>$order_id);
			output_json($output);

		}
		else{
			//��Ϊ������ѯ������󣬳����ˡ� 
			$output = array('status'=>"0",'msg'=>'΢�Ŷ�����ѯ�����Ҳ�����Ӧ�����ݡ�','payment_no'=>$order_id) ;
			output_json($output);

		}

	}
	else{
		//��ѯ�������  ������΢��������ʱ��
		$output = array('status'=>"-1",'msg'=>'�ȴ�ȷ��','payment_no'=>$order_id) ;				
		output_json($output);

	}

}
else if( $payment_info['status'] == 8 ){

	$output = array('status'=>"1",'msg'=>'֧���ɹ�','payment_no'=>$order_id) ;
	output_json($output);

}

?>
