<?php
/*
 * 发送验证码
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ("../../protocol_common.inc.php");

// 获取客户端的数据
$cp = new poco_communication_protocol_class ();
// 获取用户的授权信息
$client_data = $cp->get_input ();

$phone = $client_data ['data'] ['param'] ['phone'];
$reset = $client_data ['data'] ['param'] ['reset'];


//绑定发送验证码  用户必须没绑定过手机 当前需要绑定的手机未被人绑定过

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$check_phone_ret = $pai_user_obj->check_phone_format ( $phone );
if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_format_error',0 );
}


$check_phone_ret = $pai_user_obj->check_cellphone_exist ( $phone );

if($reset==1)
{
	//重置密码，没注册的号码不发短信
	if (!$check_phone_ret)
	{
		yue_app_out_put('phone_not_reg',-1);
	}
}
else
{
	//绑定了就不能再绑 
	if ($check_phone_ret)
	{
		yue_app_out_put('phone_is_bind',-1);
	}
}



//发送校验码
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );

if($reset==1)
{
	$group_key = 'G_PAI_USER_PASSWORD_VERIFY';
	$ret = $pai_sms_obj->send_verify_code($phone, $group_key, array());
}
else
{
	$ret = $pai_sms_obj->send_phone_reg_verify_code ( $phone );
}
if (! $ret)
{
	yue_app_out_put ( 'verify_code_sent_error',0 );
}
else
{
	yue_app_out_put ( 'verify_code_sent_success', 1 );
}



?>

