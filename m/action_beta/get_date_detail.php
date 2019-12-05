<?php

/**
 * 获取约拍详细信息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
	
/**
 * 页面接收参数
 */
$date_id = intval($_INPUT['date_id']) ;
/*
 * 根据date_id取约拍详细信息
 * @param $date_id int
 */
$ret = get_date_by_date_id($date_id);

$output_arr['data'] = $ret;

mobile_output($output_arr,false);

?>