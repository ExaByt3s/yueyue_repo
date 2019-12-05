<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/3/5
 * Time: 10:54
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$access_token   = $client_data['data']['param']['access_token'];
$v_id           = $client_data['data']['param']['v_id'];

$follow_obj = POCO::singleton ( 'pai_user_follow_class' );

$return_str['result'] = 'ERR';

if($user_id && $v_id)
{
    if($user_id == $v_id)
    {
        $return_str['result'] = 'ERR';
    }else{
        if($follow_obj->check_user_follow($user_id, $v_id))
        {
            if($follow_obj->cancel_follow($user_id, $v_id)) $return_str['result'] = 'NO FOLLOW';
        }else{
            if($follow_obj->add_user_follow($user_id, $v_id)) $return_str['result'] = 'FOLLOW';
            if($follow_obj->check_user_follow($v_id, $user_id)) $return_str['result'] = 'MUTUAL FOLLOW';
        }
    }
}

$options['data'] = $return_str;
$cp->output($options);

?>

