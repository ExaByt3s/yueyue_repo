<?php
include_once("../../poco_app_common.inc.php");

$act        = $_POST['act'];

if($act == 'add')
{
    $user_type  = $_POST['user_type'];
    $user_name  = $_POST['user_name'];
    $user_tel   = $_POST['user_tel'];
    
    if(!$user_name)
    {
        echo "<script>alert('���ֲ���Ϊ�գ�');</script>";
        exit();
    }

    if(!$user_tel)
    {
        echo "<script>alert('�绰����Ϊ�գ�');</script>";
        exit();
    }
    
    $topic_obj = POCO::singleton('pai_topic_class'); 
    if($topic_obj->add_topic_sign_up($user_name, $user_tel, $user_type))
    {
    	include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");
        $user_tel = (int)$user_tel;
        if(strlen($user_tel) == 11)
        {
            $phone = $user_tel;
            if($user_type == 'cameraman')
            {
                $content = '��л�����롶˽�������ڶ����������Ѿ��ɹ���ò����ʸ񣬻��ʼǰ���ǽ���֪ͨ������Լ�������ڴ���';
            }else{
                $content = '��л�����롶˽�������ڶ����������Ѿ���Ϊ��Yue��Ů���ѡ�ˣ�ԼԼ������Ա���ᾡ��֪ͨ������ѡ���';
            }
            $product_type = 0;//Ĭ��0��0����������ʱͨ����1�������ű���ͨ����10Ħ��ʱ������ʱͨ����11Ħ��ʱ���ű���ͨ��
            $sms_obj = POCO::singleton('class_sms_v2');
            $ret = $sms_obj->save_and_send_sms($phone, $content, $product_type);    
        }
        
        
        echo "<script>alert('�ύԤԼ�ɹ�����ȴ�������Ա��ϵ');location.href='index.html';</script>";
    }else{
        echo "<script>alert('���ֻ������ѱ�����');</script>";
    }   
}
?>