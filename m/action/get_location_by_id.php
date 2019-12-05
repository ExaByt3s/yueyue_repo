<?php

/**
 * 根据获取地理信息
 * hudw 2014.10.22
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//地区引用
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$location_id = $_INPUT['location_id'];

$location_id_info = get_poco_location_name_by_location_id ( $location_id, '', true );

if(!$location_id)
{
	$location_id_info = POCO::execute('member.get_ip_location_info');

	$location_id_info['level_2']['name'] = $location_id_info['city'];
	$location_id_info['level_2']['location_id'] = $location_id_info['location_id'];
}

$output_arr['data'] = $location_id_info;
// 此处为手动添加
$output_arr['jump_url'] = 'topic/31';


mobile_output($output_arr,false);