<?php
/** /* 
 * ���μ�ҳ,for pc����ͨWAP
 * 
 * author ����
 * 
 * 2015-4-2
 */
 
include_once('../config/phone_meeting_config.php');//���÷���Ӧ���ε�ID����Ǯ
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
 //ȡ��Ӧ�ò�������ʵ��
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');


//��ȡ�������
$meeting_id = (int)$_INPUT['meeting_id'];
$name = trim($_INPUT['name']);
$phone = (int)$_INPUT['phone'];
$email = trim($_INPUT['email']);
$enroll_num = (int)$_INPUT['num'];
$active_word = (int)$_INPUT['active_word'];//��֤��



//У�鳡��ID
if(!in_array($meeting_id,$meeting_array))
{
    echo "<script>parent.alert('����ID����');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";//���󳡴�ID����
    exit();
}


//У���ֻ�
if(empty($name))
{
    
    echo "<script>parent.alert('��������');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();

}

//У���ֻ�
if(empty($phone))
{
    
    echo "<script>parent.alert('�ֻ���������');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();

}

//У������
$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
if ( !preg_match( $pattern, $email ) )
{
    
    echo "<script>parent.alert('���䲻�Ϸ�');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();
}

//У������
if($enroll_num<1)
{
    echo "<script>parent.alert('������������Ϊ0');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();
}


//У����֤��
if(empty($active_word))
{
    echo "<script>parent.alert('��������֤��');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
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
        echo "<script>parent.alert('��֤�벻��ȷ');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
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
    echo "<script>parent.alert('�������ִ���');</script>";
    exit();
}

//��Ӧ��subject���֣�
$subject = $meeting_name_array[$meeting_id];//ƥ�����õ���

//�����豸����֧����ʽ
/**
 * �жϿͻ���
 */
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;

$__is_mobile = ($__is_android || $__is_iphone) ? true : false;

if($__is_mobile)
{
    $third_code = 'alipay_wap';
}
else
{
    $third_code = 'alipay';
}


//֧��ģ��
if(!empty($meeting_price_array[$meeting_id]))//��Ӧ����ƥ��ĵ���Ǯ
{
    
    
    
    $channel_rid = $summit_meeting_id; //ģ�����ID������ID��������
    $subject = $subject; //��Ʒ���ơ������
    $amount = $sum_price; //֧�����
    //$channel_return = 'http://www.yueus.com/topic/meeting/pay_return.php'; //֧���ɹ���ҳ����ת����
    $channel_notify = 'http://www.yueus.com/topic/meeting/pay_notify.php'; //�������첽֪֧ͨ��״̬
    $channel_return = 'http://www.yueus.com/topic/meeting/photo_meeting_middle_jump.php'; //�������첽֪֧ͨ��״̬
    

    $channel_module = 'meeting'; //ģ�����ͣ����
    $third_code = $third_code; //֧����ʽ��֧����
    $payment_info = array(
        'third_code' => $third_code,
        'subject' => 'ԼԼ-' . $subject,
        'amount' => $amount,
        'channel_return' => $channel_return,
        'channel_notify' => $channel_notify,
    );
    $payment_obj = POCO::singleton('pai_payment_class');
    $payment_ret = $payment_obj->submit_payment($channel_module, $channel_rid, $payment_info);
    if( $payment_ret['error']===0 )
    {
        $request_data = trim($payment_ret['request_data']);
        echo '<script>parent.location.href="'.$request_data.'"</script>';
    }
    else
    {
        print_r($payment_ret);
    }
    
  
    
}

 ?>