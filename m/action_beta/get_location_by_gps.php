<?php

/**
 * 根据获取地理信息
 * hudw 2014.10.22
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$long = $_INPUT['long'];
$lat = $_INPUT['lat'];



$location_info = yueyue_get_location_by_coordinate($lat, $long);


$city = $location_info['city'];


$ret = POCO::execute('common.get_location_2_location_id', $city);


$output_arr['data'] = $ret;


mobile_output($output_arr,false);