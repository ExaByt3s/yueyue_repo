<?php

/**
 * 获取用户信息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	die('no login');
}


$obj       = POCO::singleton('pai_user_class');

$ret = $obj->get_user_info_by_user_id($yue_login_id);

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