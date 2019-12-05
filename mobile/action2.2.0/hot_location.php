<?php
/**
 * 热门 地区
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$idx=0;

$ret[0]['word'] = '已开通服务城市';

$hot_city = include_once('/disk/data/htdocs232/poco/pai/config/hot_city_config.php');

foreach ($hot_city as $key => $value) 
{

	$ret[0]['city'][$idx]['city'] = $value; 
	$ret[0]['city'][$idx]['location_id'] = $key;
  
    $idx++;
}

$output_arr['data'] = $ret;

mobile_output($output_arr,false);

?>