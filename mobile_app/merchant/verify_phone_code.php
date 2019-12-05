<?php
/*
 * 验证验证码
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

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

