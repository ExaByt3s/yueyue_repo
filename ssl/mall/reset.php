<?php
/*
 * 重置密码
 */
require('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// 获取客户端的数据
$cp = new yue_protocol_system();
// 获取用户的授权信息
$client_data = $cp->get_input ( array ('be_check_token' => false ) );

$phone = $client_data ['data'] ['param'] ['phone'];
$verify_code = $client_data ['data'] ['param'] ['verify_code'];
$pwd = $client_data ['data'] ['param'] ['pwd'];
$app_name = $client_data ['data'] ['param'] ['app_name'];
$app_name = empty($app_name) ? $client_data ['data'] ['app_name'] : $app_name;
$app_name = $app_name ? $app_name : 'poco_yuepai_android';

if (! $phone)
{
	yue_app_out_put ( 'phone_is_null',0 );
}

if (! $pwd)
{
	yue_app_out_put ( 'pwd_is_null',0 );
}

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$check_phone_ret = $pai_user_obj->check_phone_format ( $phone );

if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_format_error',0 );
}

$check_phone_ret = $pai_user_obj->check_cellphone_exist ( $phone );

if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_has_not_reg',0 );
}

if (strlen ( $verify_code ) < 1)
{
	yue_app_out_put ( 'verify_code_error' ,0);
}

//检查验证码
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
//$ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code );

$group_key = 'G_PAI_USER_PASSWORD_VERIFY';
$ret = $pai_sms_obj->check_verify_code($phone, $group_key, $verify_code);

if (! $ret)
{
	yue_app_out_put ( 'verify_code_check_error',0 );
}


$ret = $pai_user_obj->update_pwd_by_phone ( $phone, $pwd );


$user_id = $pai_user_obj->get_user_id_by_phone ( $phone );


yue_app_out_put ( 'reset_pwd_success', 1 );

?>