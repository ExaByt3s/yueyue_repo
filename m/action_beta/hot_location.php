<?php
/**
 * ���� ����
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$has_open_city = '�ѿ�ͨ�������';

$hot_city = include_once('/disk/data/htdocs232/poco/pai/config/hot_city_config.php');

$output_arr['data'] = $hot_city;

$output_arr['title'] = $has_open_city;

mobile_output($output_arr,false);

?>