<?php

/**
 * 获取用户信息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$event_id = intval($_INPUT['event_id']);
$enroll_id = intval($_INPUT['enroll_id']);

$ret = get_act_ticket_detail($event_id,$enroll_id);

$output_arr['data'] = $ret;

mobile_output($output_arr,false);

?>