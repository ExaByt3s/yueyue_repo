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
            	echo "<script>alert('手机格式错误')</script>";
            	exit;
            }
            
            $check_phone_ret	= $pai_user_obj->check_cellphone_exist($phone);
            if( $check_phone_ret )
            {
            	echo "<script>alert('该手机已绑定')</script>";
            	exit;
            }
            
            //注册帐号
            $user_info_arr['nickname'] = "手机用户".substr($phone,-4);
            $user_info_arr['cellphone'] = $phone;
            $user_info_arr['role'] = 'model';
            $user_info_arr['pwd'] = $pwd;
            $user_id = $pai_user_obj->create_account($user_info_arr,$err_msg);
            
            $model_card_obj = POCO::singleton ( 'pai_model_card_class' );
        	$insert_model ["user_id"] = $user_id;
        	$model_card_obj->add_model_card ( $insert_model );
            
            $pai_user_obj->add_bind_phone_log( $user_id,$phone,'BIND_PHONE' );
            
            //帮用户登录
			$pai_user_obj->load_member($user_id);
 
 if($agent) 
 {          
     $sql_str = "INSERT IGNORE INTO pai_topic_db.pai_topic_agent_tbl(`agent_name`, `agent_id`, `app_id`) 
                 VALUES ('推荐人', '1', $user_id)";
     db_simple_getdata($sql_str, TRUE, 101);           
 }         
            
            print_message_jump_url('注册成功', 'index.php');
        }else{
            print_message_jump_url('验证码出错', 'regedit.php');
        }        
    }else{
        if(empty($code))
        {
            print_message_jump_url('缺少验证码！', 'regedit.php');            
        }

        if(empty($phone))
        {
            print_message_jump_url('缺少手机号码！', 'regedit.php');            
        }
        
        if(empty($pwd))
        {
            print_message_jump_url('缺少密码！', 'regedit.php');            
        }
    }

}




$tpl = new SmartTemplate("regedit.tpl.html");

$agent = $_GET['agent'];

if($agent)
{
    $tpl->assign('is_agent', 1);
    $tpl->assign('agent_name', '推荐人');    
}

$tpl->output();
?>

