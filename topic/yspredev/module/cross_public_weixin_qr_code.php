<?php
/** /*
 * ���μ�ҳ,for ΢��
 *
 * author ����
 *
 * 2015-4-2
 */


include_once('../config/topic_config.php');//���÷���Ӧ���ε�ID����Ǯ
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
//Լ�����
$pai_yueshe_topic_obj   = POCO::singleton('pai_yueshe_topic_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');

//����ҳ��

$order_id = (int)$_INPUT['order_id'];

//У�鳡��ID




$order_info = $pai_yueshe_topic_obj->get_order_info($order_id);


if(empty($order_info['id']) || $order_info['id']<=0)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "enroll_id error";
    mobile_output($output_arr, false);
    exit();
}

$sum_price = ($topic_price_array[$order_info['topic_id']])*$order_info['enroll_num'];//�ܼ۸�
//��Ӧ��subject���֣�
$subject = $topic_name_array[$order_info['topic_id']];//ƥ�����õ���
$amount = $sum_price; //֧�����


$channel_rid = $order_info['id']; //ģ�����ID������ID��������
$subject = $subject; //��Ʒ���ơ������
$amount = $amount; //֧�����
$channel_return = '../enroll_success.php'; //֧���ɹ���ҳ����ת����
$channel_notify = 'http://www.yueus.com/topic/yspredev/pay_notify.php'; //�������첽֪֧ͨ��״̬
$openid = trim($_COOKIE['yueus_openid']);
if( strlen($openid)<1 )
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = 'need weixin authorize!';
    mobile_output($output_arr, false);
    exit();
}

$channel_module = 'yueshe_topic_ys'; //ģ�����ͣ����
$third_code = 'tenpay_wxpub_codeurl'; //֧����ʽ��΢��֧��PUB
$payment_info = array(
    'third_code' => $third_code,
    'subject' => 'ԼԼ-' . $subject,
    'amount' => $amount,
    'channel_return' => $channel_return,
    'channel_notify' => $channel_notify,
    'openid' => $openid,
);
$payment_obj = POCO::singleton('pai_payment_class');
$payment_ret = $payment_obj->submit_payment($channel_module, $channel_rid, $payment_info);
if( $payment_ret['error']===0 )
{
    $output_arr = array();
    $output_arr['code'] = 1;
    $output_arr['message'] = '';
    $output_arr['payment_no'] = $payment_ret['payment_no'];    
    $output_arr['channel_return'] = $channel_return;
	$ecpay_activity_code_obj = POCO::singleton('pai_activity_code_class');
	$qr_link = $ecpay_activity_code_obj->get_qrcode_img($payment_ret['request_data']);
	$output_arr['data'] = $qr_link;	
	header('Content-type: image/png');
	echo file_get_contents($output_arr['data']);
	exit;
}
else
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = $payment_ret['message'];
}
mobile_output($output_arr, false);
exit();

//����ҳ��








?>