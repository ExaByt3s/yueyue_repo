<?php
/**
 * hudw 2014.10.27
 * 活动订单页
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$event_id = intval($_INPUT['event_id']);

//$event_id = 39999;// test

$ret = get_event_by_event_id($event_id);

$output_arr['data'] = $ret;

mobile_output($output_arr,false);
?>