<?php
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$user_id            = $client_data['data']['param']['user_id'];

$data['location_id']        = $location_id;
$data['hot_page']           = 'true';
$data['find_page']          = 'false';

if($location_id == 101029001) $data['find_page'] = 'true';

$options['data'] = $data;

$cp->output($options);
?>

