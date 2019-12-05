<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton('pai_user_class');
$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$user_id_str = $client_data ['data'] ['param'] ['user_id'];

$user_id_str = htmlspecialchars_decode($user_id_str);
$user_arr = explode(',',$user_id_str);

var_dump($user_id_str);
print_r($user_arr);

foreach($user_arr as $k=>$user_id)
{
    $seller_nickname = get_seller_nickname_by_user_id ( $user_id );
    $nickname = get_user_nickname_by_user_id ( $user_id );

    $result[$k]['user_id'] = $user_id;
    $result[$k]['seller_nickname'] = "{$seller_nickname}";
    $result[$k]['seller_user_icon'] = get_seller_user_icon ( $user_id, 86, TRUE );
    $result[$k]['customer_nickname'] = "{$nickname}";
    $result[$k]['customer_user_icon'] = get_user_icon ( $user_id, 86, TRUE );

    $user_info = $user_obj->get_user_info($user_id);

    $result[$k]['user_level'] = $user_level_obj->get_user_level($user_id);

    $city_name = get_poco_location_name_by_location_id ( $user_info ['location_id'] );

    $result[$k]['city_name'] = $city_name;
}

$data['list'] = $result;

$options ['data'] = $data;

$cp->output ( $options );
?>