<?php

/**
 * 交易 改价
 * 
 * @author heyaohua
 * @since 2015-8-20
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];         // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];             // 当前用户ID
$order_sn = $client_data['data']['param']['order_sn'];            // 交易ID
$change_price = $client_data['data']['param']['change_price'];        // 改价金额
$change_price_reason = $client_data['data']['param']['change_price_reason']; // 改价理由

if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '交易号为空');
    $cp->output($options);
    exit;
}

if (empty($change_price)) {
    $options['data'] = array('result' => -1, 'message' => '改价金额为空');
    $cp->output($options);
    exit;
}

$pai_mall_order_obj = POCO::singleton('pai_mall_order_class');
$result = $pai_mall_order_obj->change_order_price($order_sn, $user_id, $change_price, $change_price_reason);

$options['data'] = $result;
$cp->output($options);
