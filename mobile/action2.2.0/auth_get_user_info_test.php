<?php

define('G_YUEYUE_CORE_LOGIN_SESSION_AUTHORISE', 1);

/**
 * 获取用户信息
 */
require_once('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_id = $_INPUT['user_id'];
$access_token = $_INPUT['token'];

$obj = POCO::singleton('pai_user_class');

$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
if($__is_android)
{
	$app_name = "poco_yuepai_android";
}
else
{
	$app_name = "poco_yuepai_iphone";
}

$obj->logout();

if(empty($access_token) || empty($user_id))
{
	$output_arr['msg'] = 'user_id或token为空';
	$output_arr['data'] = '';

	mobile_output($output_arr,false);
	exit;
}
$log_arr['user_id'] = $user_id;
$log_arr['access_token'] = $access_token;
pai_log_class::add_log($log_arr, 'out', 'app_login2');


// 获取客户端的数据
$cp = new yue_protocol_system();
$access_info = $cp->get_access_info($user_id, $app_name, false, false, false);

if($access_token==$access_info['access_token'])
{
	$obj->load_member($user_id);
	
	//加LOG  例子 http://yp.yueus.com/logs/201501/28_info.txt
	$log_arr['user_id'] = $user_id;
	$log_arr['header_list'] = headers_list();
	pai_log_class::add_log($log_arr, 'in', 'app_login');

	if($user_id==106801 || $user_id==100000)
	{
		pai_log_class::add_log($log_arr, 'in', 'test_app_login');
	}
}
else
{
	$output_arr['msg'] = '登录校验失败';
	$output_arr['data'] = '';

	mobile_output($output_arr,false);
	exit;
}


$ret = $obj->get_user_info_by_user_id($user_id);

foreach ($ret['model_style'] as $key => $value) 
{
	$ret['model_style'][$key]['text'] = $value['style'];

	unset($ret['model_style'][$key]['style']);
}

if(!$ret['model_style_v2'])
{
	$ret['model_style_v2'] = array();		
}

$level_arr = include('/disk/data/htdocs232/poco/pai/config/level_require_config.php');
$level_key = abs($ret['level_require']-1);
$ret['level_remark'] = $level_arr[$level_key]['remark'];

$output_arr['data'] = $ret;


mobile_output($output_arr,false);

?>