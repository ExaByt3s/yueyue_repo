<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$access_token   = $client_data['data']['param']['access_token'];
$bl_id          = $client_data['data']['param']['blacklist_id'];
$set            = $client_data['data']['param']['set'];

$blacklist_obj  = POCO::singleton( 'pai_blacklist_class' );

$data = $blacklist_obj->set_blacklist($user_id, $bl_id, $set);

if(!empty($data))
{
    $return_str['result'] = 'OK';
}else{
    $return_str['result'] = 'ERR';
}

$options['data'] = $return_str;
$cp->output($options);
?>