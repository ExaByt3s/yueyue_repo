<?php
/**
 * 提供新约约系统的接口，给活动系统使用
 */

ignore_user_abort(true);

include_once('./mobile/poco_pai_common.inc.php');

$rst = array(
	'result' => 0,
	'message' => '',
	'data' => null,
);

//检查参数
$class_name = trim($_POST['class_name']);
$method_name = trim($_POST['method_name']);
$param_str = trim($_POST['param_str']);
$param_arr = unserialize($param_str);
$b_static = intval($_POST['b_static']);
$get_hash = trim($_POST['hash']);
if( strlen($class_name)<1 || strlen($method_name)<1 || strlen($param_str)<1 || $param_arr===false || !is_array($param_arr) || strlen($get_hash)<1 )
{
	$rst['result'] = -1;
	$rst['message'] = 'fields empty.';
	echo serialize($rst);
	exit();
}

//检查签名
$yue_key = "YUE_PAI_POCO!@#789";
$hash = md5($class_name . '::' . $method_name . '::' . $param_str . '::' . $b_static . '::' . $yue_key);
if( $get_hash!=$hash )
{
	$rst['result'] = -2;
	$rst['message'] = 'hash error.';
	echo serialize($rst);
	exit();
}

//检查类名
$class_name_arr = array(
	'pai_payment_class',
	'pai_config_class',
	'pai_activity_code_class',
	'pai_relate_poco_class',
	'pai_bind_poco_class',
	'pai_coupon_class',
	'pai_sms_class',
	'pai_user_class',
	'pai_weixin_pub_class',
	'pai_model_relate_org_class',
	'pai_chat_user_info',
);
if( !in_array(strtolower($class_name), $class_name_arr, true) )
{
	$rst['result'] = -3;
	$rst['message'] = 'class not allow.';
	echo serialize($rst);
	exit();
}

//调用方法
if( $b_static )
{
	$data = call_user_func_array("{$class_name}::{$method_name}", $param_arr);
}
else
{
	$obj = POCO::singleton($class_name);
	$data = call_user_func_array(array($obj, $method_name), $param_arr);
}

$rst['result'] = 1;
$rst['message'] = 'success';
$rst['data'] = $data;
echo serialize($rst);
exit();
