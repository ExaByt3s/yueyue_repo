<?php

/**
 * ֧��
 * hdw 2014.8.29
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');




$output_arr['data'] = '';
$output_arr['code'] = -4;
$output_arr['message'] = '��Ǹ���ף�ģ����Լ��������ͣʹ��';

mobile_output($output_arr,false);

exit();
 

$payment_obj = POCO::singleton('pai_payment_class');
$data_log = array(
);
ecpay_log_class::add_log($data_log, 'begin', 'pai_weixin_pay');

$data['from_date_id']   = $yue_login_id; //��ӰʦID
$data['to_date_id']     = (int)$_INPUT['model_id'];  //ģ��ID
$data['date_status']    = 'wait';  //״̬
$data['date_time']      = strtotime(trim($_INPUT['date']));  //Լ��ʱ��
$data['date_type']      = mb_convert_encoding(trim($_INPUT['type']),'gbk','utf-8'); //��������
$data['date_style']     = mb_convert_encoding(trim($_INPUT['style']),'gbk','utf-8'); //������
$data['date_hour']      = 1;  //����ʱ��
$data['hour']           = $_INPUT['hour'];  //����ʱ��
$data['date_price']     = $_INPUT['price'];  //����
$data['limit_num']     = (int)$_INPUT['limit_num'];  //��������
$data['date_address']   = mb_convert_encoding(trim($_INPUT['address']),'gbk','utf-8'); //��ַ$data['redirect_url']   = $_INPUT['redirect_url'];
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
 * Լ���ύ����  modify hai 20140911
 * @param array $date_data 
 * array( 
 *	'from_date_id'=>'', //��ӰʦID ����ʱ��$yue_login_id ��ֵ
 *	'to_date_id'=>'',   //ģ��ID
 *	'date_status'=>'',  //״̬     ����wait
 *	'date_time'=>'',    //Լ��ʱ��
 *	'date_type'=>'',    //��������
 *	'date_style'=>'',   //������
 *	'date_hour'=>'',    //����ʱ��
 *	'date_price'=>'',   //����
 *	'date_address'=>''  //��ַ
 *
 *)
 * @param int    0Ϊȫ��֧��   1Ϊ���֧��   �������֧������Ҫ������ת������������֧�� 
 * @param int    $user_balance �û����      �����ж��û��Ƿ�ͣ��ҳ��̫��ʱ��û�ύ  ���û����䶯�����ύ
 * @param string $third_code   ������֧���ı�ʶ ����ʱ֧��΢�ź�֧����Ǯ�� alipay_purse��tenpay_wxapp ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * ����ֵ status_code Ϊ�Ƿ����  status_code -1 �������� -2�û�����б䶯   -3��ӵ�Լ�ı�ʱ��������  -4Ϊ���ɵ��������������������
 * 1Ϊ���֧���ɹ�   2Ϊ������������ɹ�������ת����������
 * message���ص���Ϣ cur_balance �����û���ǰ��ʵ���[��status_code==2���д�key] request_data ����������������ַ���[��Ҫ��������ʱ��ŷ���]
 *
 */
$ret = add_event_date_op_v2($data,$available_balance,$is_available_balance,$third_code,$redirect_url, $notify_url);
$channel_return = $redirect_url;
if( !empty($ret['payment_no']) && strpos($channel_return, '#')!==false )
{
	//����Լ�ĵ�JS�ṹ����
	$channel_return .= '/';
	$channel_return .= "payment_no_{$ret['payment_no']}";
}

//��ȡ΢��JSSDKǩ������
$app_id = 'wx25fbf6e62a52d11e';	//ԼԼ��ʽ��
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