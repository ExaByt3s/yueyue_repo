<?php
/*
 * 注册
 */
$protocol_file = '/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php';
require($protocol_file);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// 获取客户端的数据
$cp = new yue_protocol_system();
// 获取用户的授权信息
$client_data = $cp->get_input ( array ('be_check_token' => false ) );

$phone = $client_data ['data'] ['param'] ['phone'];
$pwd = $client_data ['data'] ['param'] ['pwd'];
$verify_code = $client_data ['data'] ['param'] ['verify_code'];
$app_name = $client_data ['data'] ['param'] ['app_name'];
$app_name = empty($app_name) ? $client_data ['data'] ['app_name'] : $app_name;
$app_name = $app_name ? $app_name : 'poco_yuepai_android';


//加LOG  例子 http://yp.yueus.com/logs/201501/28_ssl_reg.txt
//$log_arr['phone'] = $phone;
//$log_arr['client_data'] = $client_data;
//pai_log_class::add_log($log_arr, 'ssl_reg_ready', 'ssl_reg');

if (! $phone)
{
	yue_app_out_put ( 'phone_is_null',0 );
}

if (! $pwd)
{
	yue_app_out_put ( 'pwd_is_null',0 );
}

if (! preg_match ( "/^.{6,31}$/", $pwd ))
{
	yue_app_out_put ( 'pwd_is_error',0 );
}


$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$check_phone_ret = $pai_user_obj->check_phone_format ( $phone );

if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_format_error',0 );
}

if (strlen ( $verify_code ) < 1)
{
	yue_app_out_put ( 'verify_code_error' ,0);
}


$user_info = $pai_user_obj->get_user_by_phone ( $phone );
//检查用户是否预先导入的
if ($user_info ['pwd_hash'] != 'poco_model_db')
{
	//绑定了就不能再绑 
	$check_phone_ret = $pai_user_obj->check_cellphone_exist ( $phone );
	if ($check_phone_ret)
	{
		yue_app_out_put ( 'reg_success', 1 );
	}
}


//检查验证码
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
$ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code, 0, false );
if (! $ret)
{
	yue_app_out_put ( 'verify_code_check_error',0 );
}

$name = "手机用户_";

//注册帐号
$user_info_arr ['nickname'] = $name . substr ( $phone, - 4 );
$user_info_arr ['cellphone'] = $phone;
$user_info_arr ['role'] = "cameraman";
$user_info_arr ['pwd'] = $pwd;
$user_info_arr ['reg_from'] = "app";
$user_id = $pai_user_obj->create_mall_account ( $user_info_arr, $err_msg );

if ($user_id < 1)
{
	yue_app_out_put ( 'reg_error' ,0);
}


//清验证码
$pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code );

$log_arr2['user_id'] = $user_id;
$log_arr2['phone'] = $phone;
$log_arr2['client_data'] = $client_data;
pai_log_class::add_log($log_arr2, 'ssl_reg_success', 'ssl_reg');

yue_app_out_put ( 'reg_success', 1 );


?>