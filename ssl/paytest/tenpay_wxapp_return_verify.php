<?php
/*
 * 2014-12-03
 * ΢��֧��������֤ҳ
 * hai
 */
//֧��ϵͳ����ƽ̨
//define('G_PAI_ECPAY_DEV', 1);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
if( defined('G_PAI_ECPAY_DEV') ){
	//����ģʽ
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
	
	$info =  array('status'=>"0",'msg'=>iconv('GBK','UTF-8','�Ƿ���������'),'payment_no'=>$payment_no ) ;
	ecpay_log_class::add_log($info, 'tenpay_wxapp_verify_test', 'tenpay_wxapp_verify_info_test');
	echo json_encode($info);
	die();

}
if( $payment_info['status'] == 0 ){
	
	$tenpay_wxapp_obj 	= POCO::singleton('ecpay_tenpay_wxapp_class',array($payment_info));
	//��΢���Ǳ��ٲ�鶩��״̬ �п�����
	$order_info 		= $tenpay_wxapp_obj->get_order_info($payment_no);
	
	if( !empty($order_info) ){

		if( $order_info->ret_code === 0  && $order_info->trade_state === "0" ){
			//����״̬��1��ʾ��֧����-1��ʾ�ȴ�ȷ�ϣ�0��������ʾ����
			//΢�Ų�ѯ�صĽ��Ϊ��֧��  ���Ÿ���������ߵĶ���״̬
			$info = array('status'=>"1",'msg'=>'֧���ɹ�!','payment_no'=>$payment_no) ;

		}
		else{
			//��Ϊ������ѯ������󣬳����ˡ� 
			$info = array('status'=>"0",'msg'=>'΢�Ŷ�����ѯ�����Ҳ�����Ӧ�����ݡ�','payment_no'=>$payment_no) ;

		}

	}
	else{
		//��ѯ�������  ������΢��������ʱ��
		$info = array('status'=>"-1",'msg'=>'�ȴ�ȷ��','payment_no'=>$payment_no) ;				

	}

}
else if( $payment_info['status'] == 8 ){

	$info = array('status'=>"1",'msg'=>'֧���ɹ�','payment_no'=>$payment_no) ;

}
$info['msg'] = iconv('GBK','UTF-8',$info['msg']);
echo json_encode($info);
ecpay_log_class::add_log($info, 'tenpay_wxapp_verify_test', 'tenpay_wxapp_verify_info_test');

?>
