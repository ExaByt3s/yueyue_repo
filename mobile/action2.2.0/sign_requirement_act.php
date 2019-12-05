<?php

/**
 * ģ�ر�������
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$model_enroll_obj = POCO::singleton ( 'pai_model_oa_enroll_class' );
$pai_user_obj = POCO::singleton ( 'pai_user_class' );

if(!$yue_login_id)
{
	die('no_login');
}

/**
 * ҳ����ղ���
 */
$order_id = intval($_INPUT['order_id']);


$role = $pai_user_obj->check_role($yue_login_id);
if($role!='model')
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = "��Ӱʦ���ܱ���";
	mobile_output($output_arr,false);
	exit;
}


$check_sign = $model_enroll_obj->check_repeat($yue_login_id,$order_id);

if($check_sign)
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = "�ѱ�������";
	mobile_output($output_arr,false);
	exit;
}


$ret = $model_enroll_obj->add_model_enroll($yue_login_id,$order_id);

if($ret)
{
	$code = 1;
}
else
{
	$code = 0;
}


$output_arr['code'] = $code;


mobile_output($output_arr,false);

?>