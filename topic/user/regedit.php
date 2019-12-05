<?php
include_once("../../poco_app_common.inc.php");
include_once("pai_topic_common.inc.php");

$act = $_POST['act'];
if ($act == 'regedit') {
    $code       = $_POST['code'];
    $phone      = trim($_POST['phone']);
    $pwd        = trim($_POST['pwd']);
    $agent      = $_POST['agent'];
    
    if($code && $phone && $pwd)
    {
        $pai_sms_obj = POCO::singleton('pai_sms_class');
        $ret = $pai_sms_obj->check_phone_reg_verify_code($phone, $code);
        if($ret)
        {
            $pai_user_obj 		= POCO::singleton('pai_user_class');
            $check_phone_ret	= $pai_user_obj->check_phone_format($phone); 

            if( !$check_phone_ret ){
            	echo "<script>alert('�ֻ���ʽ����')</script>";
            	exit;
            }
            
            $check_phone_ret	= $pai_user_obj->check_cellphone_exist($phone);
            if( $check_phone_ret )
            {
            	echo "<script>alert('���ֻ��Ѱ�')</script>";
            	exit;
            }
            
            //ע���ʺ�
            $user_info_arr['nickname'] = "�ֻ��û�".substr($phone,-4);
            $user_info_arr['cellphone'] = $phone;
            $user_info_arr['role'] = 'model';
            $user_info_arr['pwd'] = $pwd;
            $user_id = $pai_user_obj->create_account($user_info_arr,$err_msg);
            
            $model_card_obj = POCO::singleton ( 'pai_model_card_class' );
        	$insert_model ["user_id"] = $user_id;
        	$model_card_obj->add_model_card ( $insert_model );
            
            $pai_user_obj->add_bind_phone_log( $user_id,$phone,'BIND_PHONE' );
            
            //���û���¼
			$pai_user_obj->load_member($user_id);
 
 if($agent) 
 {          
     $sql_str = "INSERT IGNORE INTO pai_topic_db.pai_topic_agent_tbl(`agent_name`, `agent_id`, `app_id`) 
                 VALUES ('�Ƽ���', '1', $user_id)";
     db_simple_getdata($sql_str, TRUE, 101);           
 }         
            
            print_message_jump_url('ע��ɹ�', 'index.php');
        }else{
            print_message_jump_url('��֤�����', 'regedit.php');
        }        
    }else{
        if(empty($code))
        {
            print_message_jump_url('ȱ����֤�룡', 'regedit.php');            
        }

        if(empty($phone))
        {
            print_message_jump_url('ȱ���ֻ����룡', 'regedit.php');            
        }
        
        if(empty($pwd))
        {
            print_message_jump_url('ȱ�����룡', 'regedit.php');            
        }
    }

}




$tpl = new SmartTemplate("regedit.tpl.html");

$agent = $_GET['agent'];

if($agent)
{
    $tpl->assign('is_agent', 1);
    $tpl->assign('agent_name', '�Ƽ���');    
}

$tpl->output();
?>

