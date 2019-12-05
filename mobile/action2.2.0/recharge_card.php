<?php
/**
 * hudw 2014.9.1
 * ��ϸҳ 
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//��־
pai_log_class::add_log(array(), 'recharge_card begin', 'recharge');

$number = trim($_INPUT['number']);
$pwd = trim($_INPUT['pwd']);

if(!$yue_login_id)
{
	$output_arr['code'] = 0;
	$output_arr['message'] = "no login";
	$output_arr['data'] = "";
	mobile_output($output_arr,false);
	exit();
}

//ʹ�ó�ֵ��
$payment_obj = POCO::singleton('pai_payment_class');
$use_ret = $payment_obj->use_card($yue_login_id, $number, $pwd);
if( $use_ret['result']!=1 )
{
	$output_arr['code'] = 0;
	$output_arr['message'] = $use_ret['message'];
	$output_arr['data'] = "";
}
else
{
	$output_arr['code'] = 1;
	$output_arr['message'] = "��ֵ�ɹ�";
	$output_arr['data'] = "";
}

//��־
pai_log_class::add_log($output_arr, 'recharge_card end', 'recharge');

mobile_output($output_arr,false);
?>