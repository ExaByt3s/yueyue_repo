<?php
/*
 * 验证验证码
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ("../../protocol_common.inc.php");

// 获取客户端的数据
$cp = new poco_communication_protocol_class ();
// 获取用户的授权信息
$client_data = $cp->get_input ( array ('be_check_token' => false ) );

$phone = $client_data ['data'] ['param'] ['phone'];
$verify_code = $client_data ['data'] ['param'] ['verify_code'];
$reset = $client_data ['data'] ['param'] ['reset'];

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$check_phone_ret = $pai_user_obj->check_phone_format ( $phone );
if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_format_error' ,0);
}

if (strlen ( $verify_code ) < 1)
{
	yue_app_out_put ( 'verify_code_error',0 );
}



//检查验证码
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );

if($reset==1)
{
	$group_key = 'G_PAI_USER_PASSWORD_VERIFY';
	$ret = $pai_sms_obj->check_verify_code($phone, $group_key, $verify_code);
}
else
{
	$ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code, 0, false );
}

if (! $ret)
{
	yue_app_out_put ( 'verify_code_check_error',0 );
}
else
{

	yue_app_out_put ( 'verify_code_check_success', 1 );
}
?>

