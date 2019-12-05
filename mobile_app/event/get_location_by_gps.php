<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$long        = $client_data['data']['param']['long'];
$lat         = $client_data['data']['param']['lat'];

$location_info = yueyue_get_location_by_coordinate($lat, $long);

$location_info['location_id'] = POCO::execute('common.get_location_2_location_id', $location_info['city']);

$options['data'] = $location_info;
$cp->output($options);
?>