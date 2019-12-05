<?php 

/**
 * 文案输出列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$help_text_config = include_once('/disk/data/htdocs232/poco/pai/mobile/config/help_text.conf.php');

$type = $_INPUT['type'];


$output_arr['data'] = $help_text_config['text'][$type];
$output_arr['code'] = 1;
$output_arr['msg'] = 'success';

mobile_output($output_arr,false);

?>