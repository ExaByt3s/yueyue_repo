<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$access_token   = $client_data['data']['param']['access_token'];

$blacklist_obj  = POCO::singleton( 'pai_blacklist_class' );

$data = $blacklist_obj->get_blacklist_list($user_id, $bl_id, $set);
$data_array = json_decode($data, true);
if($data_array[retcode] != '4000')
{
    foreach($data_array[data] AS $key=>$val)
    {
        $blacklist['user_id'] = $val;
        $data_list['list'][] = $blacklist;
    }
    $options['data'] = $data_list;
}else{
    $data_list['list'] = array();
    $options['data'] = $data_list;

}
$cp->output($options);
?>