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

$topic_id = (int)$_INPUT['topic_id'];
$name = trim($_INPUT['name']);
$phone = (int)$_INPUT['phone'];
$address = trim($_INPUT['address']);
$enroll_num = (int)$_INPUT['enroll_num'];
//У�鳡��ID
if(!in_array($topic_id,$topic_array))
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "data error";
    mobile_output($output_arr, false);
    exit();
}

//У���ֻ�
if(empty($name))
{

    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "name error";
    mobile_output($output_arr, false);
    exit();

}


//У���ֻ�
if(empty($phone))
{

    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "phone error";
    mobile_output($output_arr, false);
    exit();

}

//У���ַ
if ( empty($address) )
{

    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "address error";
    mobile_output($output_arr, false);
    exit();
}

//У������
if($enroll_num<1)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "enroll number error";
    mobile_output($output_arr, false);
    exit();
}




//��������
$insert_data['topic_id'] = $topic_id;
$insert_data['name'] = $name;
$insert_data['address'] = $address;
$insert_data['phone'] = $phone;
$insert_data['enroll_num'] = $enroll_num;
$sum_price = ($topic_price_array[$topic_id])*$enroll_num;//�ܼ۸�
$insert_data['sum_price'] = $sum_price;
$insert_data['user_id'] = $yue_login_id;
$enroll_tmp_id = $pai_yueshe_topic_obj->add_order($insert_data);
if(empty($enroll_tmp_id) || $enroll_tmp_id<=0)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "enroll_id error";
    mobile_output($output_arr, false);
    exit();
}
//��Ӧ��subject���֣�
$subject = $topic_name_array[$topic_id];//ƥ�����õ���
$amount = $sum_price; //֧�����


$channel_rid = $enroll_tmp_id; //ģ�����ID������ID��������
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
$third_code = 'tenpay_wxpub'; //֧����ʽ��΢��֧��PUB
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
    $output_arr['data'] = $payment_ret['request_data'];
    $output_arr['channel_return'] = $channel_return;
}
else
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = $payment_ret['message'];
}
$output_arr['order_id'] = $enroll_tmp_id;//���ض���ID�����ڱ�����֧����������¼
mobile_output($output_arr, false);
exit();

//����ҳ��








?>