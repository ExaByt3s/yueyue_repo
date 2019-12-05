<?php
/**
 * hudw 2014.8.20
 * 输出地区数据，按字母排列，此接口只用于获取数据并存放在本地
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$city_obj    = POCO::singleton('pai_city_class');

$output_arr['data'] = $city_obj->get_city_list();

mobile_output($output_arr,false); 
?>