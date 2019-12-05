<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$limit              = $client_data['data']['param']['limit'];
$user_id            = $client_data['data']['param']['user_id'];

$topic_obj = POCO::singleton ('pai_topic_class');

$location['name'] = '广州';
$location['location_id'] = 101029001;
$data[] = $location;

/**
$location['name'] = '武汉';
$location['location_id'] = 101019001;
$data[] = $location;
**/

$location['name'] = '北京';
$location['location_id'] = 101001001;
$data[] = $location;

$location['name'] = '上海';
$location['location_id'] = 101003001;
$data[] = $location;

$location['name'] = '成都';
$location['location_id'] = 101022001;
$data[] = $location;

$location['name'] = '重庆';
$location['location_id'] = 101004001;
$data[] = $location;

$location['name'] = '西安';
$location['location_id'] = 101015001;
$data[] = $location;

/**
$location['name'] = '新疆';
$location['location_id'] = 101024001;
$data[] = $location;
**/
//$test_user_array = array('100008', '131184', '113899', '100067', '129291');
//if(in_array($user_id, $test_user_array))
{
    $location['name'] = '深圳';
    $location['location_id'] = 101029002;
    $data[] = $location;
}

//版本号整理，去掉后续

$array_str  = explode('_', $client_data['data']['version']);
$appver     = $array_str[0];
if(version_compare($appver, '3.2.10', '='))
{
    unset($data);
    $location['name'] = '广州';
    $location['location_id'] = 101029001;
    $data[] = $location;


}


if(version_compare($client_data['data']['version'], '2.2.0_r3', '='))
{
    unset($data);
    $location['name'] = '广州';
    $location['location_id'] = 101029001;
    $data[] = $location;
}


$options['data'] = $data;
$cp->output($options);
?>