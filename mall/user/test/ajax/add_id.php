<?php
ignore_user_abort ( true );
/**
 * 添加身份证图片
 */

include_once('../common.inc.php');

/**
 * 页面接收参数
 */
$img = trim($_INPUT ['img']);
$id_code = trim($_INPUT ['id_code']);
$name = trim($_INPUT ['name']);

if (empty($yue_login_id))
{
	$output_arr ['code'] = 0;
	$output_arr ['msg'] = '尚未登录';
	
	mobile_output ( $output_arr, false );
	exit ();
}

if (! $img)
{
	$output_arr ['code'] = 0;
	$output_arr ['msg'] = '请上传图片';
	
	mobile_output ( $output_arr, false );
	exit ();
}

// 提交审核
$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );

$insert_data ['user_id'] = $yue_login_id;
$insert_data ['img'] = $img;
$insert_data ['id_code'] = $id_code;
$insert_data ['name'] = $name;

$ret = $id_audit_obj->add_audit ( $insert_data );

$output_arr ['code'] = $ret > 0 ? 1 : 0;
$output_arr ['msg'] = $ret ? '提交成功' : '提交失败';
$output_arr ['data'] = array(
	"url" => './index.php?shz=v2'
);

mall_mobile_output ( $output_arr, false );

?>