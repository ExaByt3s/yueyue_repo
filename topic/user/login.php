<?php
header("Location:http://www.yueus.com/model/login.php");
exit();
include_once("../../poco_app_common.inc.php");
include_once("pai_topic_common.inc.php");



$act = $_POST['act'];
if($act == 'login')
{
    $phone = $_POST['phone'];
    $pwd   = $_POST['pwd'];
    $user_id = check_user($phone, $pwd);
    if($user_id)
    {
        //�û���¼
        $pai_user_obj 		= POCO::singleton('pai_user_class');
        $pai_user_obj->load_member($user_id);
        print_message_jump_url('��½�ɹ�', 'index.php');
    }else{
        print_message_jump_url('��½ʧ��', 'index.php');
    }
}



$tpl = new SmartTemplate("login.tpl.html");

$tpl->output();
?>

