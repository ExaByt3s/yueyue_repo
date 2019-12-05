<?php
/**
 * 获取cookie的session_ip_location
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$ret = POCO::execute('member.get_ip_location_info');

$output_arr['data'] = $ret;

mobile_output($output_arr,false);

?>