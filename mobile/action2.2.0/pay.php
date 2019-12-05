<?php

/**
 * ֧��
 * hdw 2014.8.29
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$date_id = $_INPUT['date_id'];
$third_code = $_INPUT['third_code'];

$coupon_sn = $_INPUT['coupon_sn'];

$available_balance = $_INPUT['available_balance'];
$is_available_balance = $_INPUT['is_available_balance'];
$redirect_url = urldecode($_INPUT['redirect_url']);
$notify_url = G_PAI_APP_DOMAIN . '/mobile/' . basename(dirname(__FILE__)) . '/pay_date_notify.php';



if(in_array($yue_login_id, unserialize(TEST_USER_ACCOUNT)))
{
	$output_arr['data'] = array();
	$output_arr['code'] = 1;
	$output_arr['message'] = 'test pay ok';

	mobile_output($output_arr,false);

	exit();
}

$check_ua=preg_match('#(yue_pai/3\.0\.10|yue_pai/3\.0\.0|yue_pai 3\.0\.0|yue_pai 3\.0\.10)#',$_SERVER['HTTP_USER_AGENT']);
if($check_ua)
{
    $output_arr['code'] = 0;
    $output_arr['message'] = "��Ǹ���ף�ģ����Լ��������ͣʹ��";
    mobile_output($output_arr,false);
    exit();
}

/**
 * Լ���ύ����
 * @param int $date_id 
 * @param int    0Ϊȫ��֧��   1Ϊ���֧��   �������֧������Ҫ������ת������������֧�� 
 * @param int    $available_balance �û����      �����ж��û��Ƿ�ͣ��ҳ��̫��ʱ��û�ύ  ���û����䶯�����ύ
 * @param string $third_code   ������֧���ı�ʶ ����ʱ֧��΢�ź�֧����Ǯ�� alipay_purse��tenpay_wxapp ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $notify_url
 * @param string $coupon_sn
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * ����ֵ status_code Ϊ�Ƿ����  status_code -1 �������� -2�û�����б䶯   -3��ӵ�Լ�ı�ʱ��������  -4Ϊ���ɵ��������������������
 * 1Ϊ���֧���ɹ�   2Ϊ������������ɹ�������ת����������
 * message���ص���Ϣ cur_balance �����û���ǰ��ʵ���[��status_code==2���д�key] request_data ����������������ַ���[��Ҫ��������ʱ��ŷ���]
 *
 */

$ret = update_date_op($date_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url = '',$coupon_sn);

$channel_return = $redirect_url;
if( !empty($ret['payment_no']) && strpos($channel_return, '#')!==false )
{
	//����Լ�ĵ�JS�ṹ����
	$channel_return .= '/';
	$channel_return .= "payment_no_{$ret['payment_no']}";
}

$output_arr['third_code']   = $third_code;
$output_arr['data'] 		= $ret['request_data'];
$output_arr['payment_no'] 	= $ret['payment_no'];
$output_arr['channel_return'] = $channel_return;
$output_arr['code'] 		= $ret['status_code'];
$output_arr['message'] 		= $ret['message'];
$output_arr['cur_balance']  = $ret['cur_balance'];

//��־
pai_log_class::add_log(array('ret'=>$ret,'output_arr'=>$output_arr), 'pay end', 'pay');

mobile_output($output_arr,false);

?>