<?php
/** /* 
 * ���μ�ҳ,for ΢��
 * 
 * author ����
 * 
 * 2015-4-2
 */
 
 
include_once('/disk/data/htdocs232/poco/pai/topic/meeting/config/phone_meeting_config.php');//���÷���Ӧ���ε�ID����Ǯ
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');

//����ҳ��

$meeting_id = (int)$_INPUT['meeting_id'];
$name = trim($_INPUT['name']);
$phone = (int)$_INPUT['phone'];
$email = trim($_INPUT['email']);
$enroll_num = (int)$_INPUT['num'];
$active_word = (int)$_INPUT['active_word'];
//У�鳡��ID
if(!in_array($meeting_id,$meeting_array))
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

//У������
$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
if ( !preg_match( $pattern, $email ) )
{
    
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "email error";
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

//У����֤��
if($active_word<1)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "vertify number not exist error";
    mobile_output($output_arr, false);
    exit();
}
else
{
    //У����֤��
    $verify_code = $active_word;
    $group_key = 'G_PAI_TOPIC_MEETING_VERIFY';
    $active_code_ret = $pai_sms_obj->check_verify_code($phone, $group_key, $verify_code, $user_id=0, $b_del_verify_code=true);
    if(!$active_code_ret)
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "vertify number error";
        mobile_output($output_arr, false);
        exit();
    }
    
}


//��������
$insert_data['meeting_id'] = $meeting_id;
$insert_data['name'] = $name;
$insert_data['email'] = $email;
$insert_data['phone'] = $phone;
$insert_data['enroll_num'] = $enroll_num;
$sum_price = ($meeting_price_array[$meeting_id])*$enroll_num;//�ܼ۸�
$insert_data['sum_price'] = $sum_price;
$summit_meeting_id = $summit_meeting_obj->add_summit_meeting($insert_data);
if(empty($summit_meeting_id) || $summit_meeting_id<=0)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "enroll_id error";
    mobile_output($output_arr, false);
    exit();
}
//��Ӧ��subject���֣�
$subject = $meeting_name_array[$meeting_id];//ƥ�����õ���
$amount = $sum_price; //֧�����


$channel_rid = $summit_meeting_id; //ģ�����ID������ID��������
$subject = $subject; //��Ʒ���ơ������
$amount = $amount; //֧�����
$channel_return = 'http://www.yueus.com/topic/meeting/photo_meeting_middle_jump.php'; //֧���ɹ���ҳ����ת����
$channel_notify = 'http://www.yueus.com/topic/meeting/pay_notify.php'; //�������첽֪֧ͨ��״̬
$openid = trim($_COOKIE['yueus_openid']);
if( strlen($openid)<1 )
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = 'need weixin authorize!';
    mobile_output($output_arr, false);
    exit();
}

$channel_module = 'meeting'; //ģ�����ͣ����
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
mobile_output($output_arr, false);
exit();

//����ҳ��








?>