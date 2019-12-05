<?php

/**
 * 优惠券 发放
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-9-6
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = intval($client_data['data']['param']['user_id']);

if ($user_id < 1) {
    $options['data'] = array(
        'result' => -1,
        'message' => '未知用户!',
    );
    return $cp->output($options);
}

$params = array(
    'user_id' => $user_id,
);

// 记录跳转
$trigger_obj = POCO::singleton('pai_trigger_class');
$res = $trigger_obj->app_store_comment_before($params);
if ($res) {
    $options['data'] = array(
        'result' => 1,
        'message' => '优惠券发放成功!',
    );
} else {
    $options['data'] = array(
        'result' => 0,
        'message' => '优惠券发放失败!',
    );
}
return $cp->output($options);
