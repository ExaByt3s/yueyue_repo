<?php

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");

global $yue_login_id;
/*
 * 改密码获取验证码
 * @param String $phone
 * @return 
 */
$yue_phone = $_INPUT['phone'];

$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
$ret = $pai_sms_obj->send_phone_reg_verify_code ($yue_phone);

if($ret == 1)
{
	$output_arr['code'] = 1;
	$output_arr['message'] = "发送成功";
}
else
{
	$output_arr['code'] = 0;
	$output_arr['message'] = "发送失败";
}

mobile_output($output_arr,false);
?>