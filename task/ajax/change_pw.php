<?php

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");

global $yue_login_id;
/*
 * �޸�����
 * @param String $phone
 * @return 
 */
$phone = $_INPUT['phone'];
$verify_code = $_INPUT['verify_code'];
$pwd = $_INPUT['password'];

$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
$ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code, 0, false );

if($ret == 1)
{
	$pai_user_obj = POCO::singleton ( 'pai_user_class' );
	$ret_code = $pai_user_obj->update_pwd_by_phone ( $phone, $pwd );
	if($ret_code)
	{
		$output_arr['code'] = 1;
		$output_arr['message'] = "�޸ĳɹ�";
	}
	else
	{
		$output_arr['code'] = -1;
		$output_arr['message'] = "�޸�ʧ��";
	}
	
}
else
{
	$output_arr['code'] = 0;
	$output_arr['message'] = "��֤�����";
}

mobile_output($output_arr);
?>