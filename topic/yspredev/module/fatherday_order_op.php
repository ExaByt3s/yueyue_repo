<?php
/**
 * ���׽�621ר����붩��
 * ����
 *
 * 2015-6-3
 */

include_once('../config/topic_config.php');//����ר��ID����Ǯ������
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//ȡ��Ӧ�ò�������ʵ��
//ʹ��form������Ӽ�¼
define("G_DB_GET_REALTIME_DATA",1);
//Լ�����
$pai_yueshe_topic_obj   = POCO::singleton('pai_yueshe_topic_class');
//�û����ݶ���
$pai_user_obj = POCO::singleton('pai_user_class');

if(empty($yue_login_id))
{
    header("location:../father_day_login.php");
    exit("δ��¼");
}

$topic_id = (int)$_INPUT['topic_id'];
$weixin = trim($_INPUT['weixin']);
$enroll_num = (int)$_INPUT['enroll_num'];
$name = trim($_INPUT['name']);
$address = trim($_INPUT['address']);
$take_time_one = trim($_INPUT['take_time_one']);
$take_time_two = trim($_INPUT['take_time_two']);

//��ȡ�����Ϣ
$enroll_user_phone = $pai_user_obj->get_phone_by_user_id($yue_login_id);
$phone = $enroll_user_phone;


//У�鳡��ID
if(!in_array($topic_id,$topic_array))
{
    echo "<script>parent.alert('ר��ID����');window.location.href='../fatherday_enroll.php';</script>";//���󳡴�ID����
    exit();
}

//У��΢��
if(empty($weixin))
{
    echo "<script>parent.alert('΢������');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//У�鱨������
if(empty($enroll_num))
{
    echo "<script>parent.alert('������������');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//У�鱨������
if(empty($name))
{
    echo "<script>parent.alert('��������');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//У�鱨����ַ
if(empty($address))
{
    echo "<script>parent.alert('��ַ����');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//У���ֻ���
if(empty($phone))
{
    echo "<script>parent.alert('�ֻ�������');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//У������ʱ���1
if(empty($take_time_one) || empty($take_time_two))
{
    echo "<script>parent.alert('����ʱ�������');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}



//��������

$insert_data['topic_id'] = $topic_id;
$insert_data['phone'] = $phone;
$insert_data['weixin'] = $weixin;
$insert_data['enroll_num'] = $enroll_num;
$insert_data['name'] = $name;
$insert_data['user_id'] = $yue_login_id;
//�ܼ۸�
$sum_price = ($topic_price_array[$topic_id])*$enroll_num;
$insert_data['sum_price'] = $sum_price;

$insert_data['take_time'] = $take_time_one." ".$take_time_two;


$enroll_tmp_id = $pai_yueshe_topic_obj->add_order($insert_data);
if(empty($enroll_tmp_id) || $enroll_tmp_id<=0)
{
    echo "<script>parent.alert('�������ִ���');parent.reload();</script>";//��ʱ����
    exit();
}



//����֧������
$third_code = 'alipay_wap';
$subject = $topic_name_array[$topic_id];//��Ʒ���ơ������

if(!empty($topic_price_array[$topic_id]))//��Ӧר��ƥ��ĵ���Ǯ
{
    $channel_rid = $enroll_tmp_id;
    //��Ӧ��subject���֣�
    $subject = $subject;
    $amount = $sum_price; //֧�����
    $channel_notify = './pay_notify.php'; //�������첽֪֧ͨ��״̬
    $channel_return = './enroll_success.php'; //�������첽֪֧ͨ��״̬


    $channel_module = 'yueshe_topic_ys'; //ģ�����ͣ�Լ��ר��
    $third_code = $third_code; //֧����ʽ��֧����
    $payment_info = array(
        'third_code' => $third_code,
        'subject' => 'ԼԼ��Ӱ-' . $subject,
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