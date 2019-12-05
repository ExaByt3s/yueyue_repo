<?php
/**
 * ป๑ตรหัห๗ฑ๊วฉ
 */
 include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$url = trim($_INPUT['url']);

$activity_code_obj = POCO::singleton('pai_activity_code_class');
$output_arr = $activity_code_obj->get_qrcode_img($url);

mobile_output($output_arr,false);

?>