<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');

$arr = array('province'=>$area_config['province'],'city'=>$area_config['city']);

$output_arr['data'] = $area_config['sort_area'];

$output_arr['two_lv_data'] = $arr;

mobile_output($output_arr,false);

?>