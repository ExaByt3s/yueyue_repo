<?php 

/**
 * get demand list 
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



$help_text_config = include_once('/disk/data/htdocs232/poco/pai/mobile/config/demand.conf.php');



$output_arr['data'] = $help_text_config;
$output_arr['code'] = 1;
$output_arr['msg'] = 'success';

mobile_output($output_arr,false);

?>