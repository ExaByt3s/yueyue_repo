<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$access_token   = $client_data['data']['param']['access_token'];

$obj = POCO::singleton('pai_nfc_class');

$obj->mobile_logout($user_id);


$data['tips'] = 'ÍË³ö³É¹¦!';

$options['data'] = $data;


$cp->output($options);
?>