<?php
/**
 * 获取签到码
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$code = intval($_INPUT['code']) ;

$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );

$ret = $activity_code_obj->get_scan_cache($code);

if($ret)
{
	$ret = 1;
}
else
{
	$ret = 0;
}

$output_arr['data'] = $ret;


mobile_output($output_arr,false);

?>