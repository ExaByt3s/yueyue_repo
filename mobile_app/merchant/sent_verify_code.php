<?php
/*
 * ������֤��
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$phone = $client_data ['data'] ['param'] ['phone'];
$reset = $client_data ['data'] ['param'] ['reset'];




//�󶨷�����֤��  �û�����û�󶨹��ֻ� ��ǰ��Ҫ�󶨵��ֻ�δ���˰󶨹�

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$check_phone_ret = $pai_user_obj->check_phone_format ( $phone );
if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_format_error',0 );
}

$user_info = $pai_user_obj->get_user_by_phone ( $phone );
//����û��Ƿ���ģ�ؿ⵼���
if ($user_info ['pwd_hash'] == 'poco_model_db')
{
	$is_model_db = true;
}


if($reset==1)
{
	$check_phone_ret = $pai_user_obj->check_cellphone_exist ( $phone );
	if (!$check_phone_ret)
	{
		yue_app_out_put('phone_not_reg',-1);
	}
}

if ($reset != 1 && ! $is_model_db)
{
	$check_phone_ret = $pai_user_obj->check_cellphone_exist ( $phone );
	//���˾Ͳ����ٰ� 
	if ($check_phone_ret)
	{
		yue_app_out_put('phone_is_bind',-1);
	}
}


//����У����
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

$log_arr2['phone'] = $phone;
$log_arr2['reset'] = $reset;
$log_arr2['ret'] = $ret;
pai_log_class::add_log($log_arr2, 'sent_code', 'sent_code');

if (! $ret)
{
	yue_app_out_put ( 'verify_code_sent_error',0 );
}
else
{
	yue_app_out_put ( 'verify_code_sent_success', 1 );
}


?>