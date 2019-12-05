<?php

/**
 * 活动详情
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$enroll_id = intval($_INPUT['enroll_id']);
$event_id = intval($_INPUT['event_id']);
$event_title = $_INPUT['event_title'];
$table_id = intval($_INPUT['table_id']);
$table_info = $_INPUT['table_info'];
$budget = $_INPUT['budget'];
$enroll_num = intval($_INPUT['enroll_num']);
$is_count_money = intval($_INPUT['is_count_money']);


// 直接支付流程
if($is_count_money)
{
	$ret['event_id'] = $event_id;
	$ret['event_title'] =mb_convert_encoding ($event_title,'gbk','utf-8');
	$ret['table_id'] = $table_id;
	$ret['table_info'] = $table_info;
	$ret['total_budget'] = $enroll_num*$budget;
	$ret['enroll_num'] = $enroll_num;
}
// 跑继续支付流程
else
{
	$ret = get_enroll_detail_info($enroll_id);
	$ret['table_arr'][0] = array('table_id'=>$ret['table_id']); 
}

//$ret['event_title'] = mb_convert_encoding (($ret['event_title']),'gbk','utf-8');

$output_arr['data'] = $ret;



mobile_output($output_arr,false);

?>