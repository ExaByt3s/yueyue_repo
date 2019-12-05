<?php
/*
 * ������֤��
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ("../../protocol_common.inc.php");

// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class ();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input ();

$phone = $client_data ['data'] ['param'] ['phone'];
$reset = $client_data ['data'] ['param'] ['reset'];


//�󶨷�����֤��  �û�����û�󶨹��ֻ� ��ǰ��Ҫ�󶨵��ֻ�δ���˰󶨹�

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$check_phone_ret = $pai_user_obj->check_phone_format ( $phone );
if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_format_error',0 );
}


$check_phone_ret = $pai_user_obj->check_cellphone_exist ( $phone );

if($reset==1)
{
	//�������룬ûע��ĺ��벻������
	if (!$check_phone_ret)
	{
		yue_app_out_put('phone_not_reg',-1);
	}
}
else
{
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
if (! $ret)
{
	yue_app_out_put ( 'verify_code_sent_error',0 );
}
else
{
	yue_app_out_put ( 'verify_code_sent_success', 1 );
}



?>

