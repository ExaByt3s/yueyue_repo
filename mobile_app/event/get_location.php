<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$limit              = $client_data['data']['param']['limit'];
$user_id            = $client_data['data']['param']['user_id'];

$topic_obj = POCO::singleton ('pai_topic_class');

$location['name'] = '����';
$location['location_id'] = 101029001;
$data[] = $location;

/**
$location['name'] = '�人';
$location['location_id'] = 101019001;
$data[] = $location;
**/

$location['name'] = '����';
$location['location_id'] = 101001001;
$data[] = $location;

$location['name'] = '�Ϻ�';
$location['location_id'] = 101003001;
$data[] = $location;

$location['name'] = '�ɶ�';
$location['location_id'] = 101022001;
$data[] = $location;

$location['name'] = '����';
$location['location_id'] = 101004001;
$data[] = $location;

$location['name'] = '����';
$location['location_id'] = 101015001;
$data[] = $location;

/**
$location['name'] = '�½�';
$location['location_id'] = 101024001;
$data[] = $location;
**/
//$test_user_array = array('100008', '131184', '113899', '100067', '129291');
//if(in_array($user_id, $test_user_array))
{
    $location['name'] = '����';
    $location['location_id'] = 101029002;
    $data[] = $location;
}

//�汾������ȥ������

$array_str  = explode('_', $client_data['data']['version']);
$appver     = $array_str[0];
if(version_compare($appver, '3.2.10', '='))
{
    unset($data);
    $location['name'] = '����';
    $location['location_id'] = 101029001;
    $data[] = $location;


}


if(version_compare($client_data['data']['version'], '2.2.0_r3', '='))
{
    unset($data);
    $location['name'] = '����';
    $location['location_id'] = 101029001;
    $data[] = $location;
}


$options['data'] = $data;
$cp->output($options);
?>