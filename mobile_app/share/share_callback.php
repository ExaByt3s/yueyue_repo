<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id                = $client_data['data']['param']['user_id'];
$soure_id               = $client_data['data']['param']['soure_id'];
$dmid                   = $client_data['data']['param']['dmid'];
$share_link             = $client_data['data']['param']['share_link'];
$share_type             = $client_data['data']['param']['share_type'];
$share_callback_data    = $client_data['data']['param']['share_callback_data'];

//$sql_str = "INSERT INTO pai_log_db.yueyue_shace_callback_tbl(user_id, soure_id, dmid, share_link, share_type, share_callback_data)
//            VALUES ($user_id, $soure_id, '{$dmid}', '{$share_link}', '{$share_type}', '{$share_callback_data}')";

$user_obj           = POCO::singleton('pai_user_class');
$pai_trigger_obj    = POCO::singleton('pai_trigger_class');
if($user_id)
{
    $user_info      = $user_obj->get_user_info($user_id);
    $cellphone      = $user_info['cellphone'];
    $url            = $share_link;
    $add_time       = time();

    $sql_str = "INSERT IGNORE INTO pai_db.pai_event_share_coupon_tbl(cellphone, event_id, url, add_time)
                VALUES ('{$cellphone}', '{$event_id}', '{$url}', '{$add_time}')";
    db_simple_getdata($sql_str, TRUE, 101);


    $trigger_params = array(
        'user_id' => $user_id,
        'cellphone' => $cellphone, //手机号码
        'url' => $url //分享链接
    );
    $pai_trigger_obj->app_share_after($trigger_params);

}


$return_str['result'] = 'OK';
$options['data'] =  $return_str;

$cp->output($options);
?>