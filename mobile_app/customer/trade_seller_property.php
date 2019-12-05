<?php

/**
 * 商家 属性
 *
 * @since 2015-8-12
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$seller_user_id = intval($client_data['data']['param']['seller_user_id']);  // 商家ID
$type_id = intval($client_data['data']['param']['type_id']);  // 分类ID  ( for web )

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($seller_user_id, 2);  // 获取用户信息
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$profile = $user_result['seller_data']['profile'][0];
if (empty($profile)) {
    $options['data'] = array('result' => 0, 'message' => '没有该用户属性');
    return $cp->output($options);
}
$score = $record_result['comment_score'];   // 用户评价
$score = empty($score) ? 5 : $score;  // 默认5分
$property = array(
    'type_id' => $profile['type_id'],
    // 交易信息
    'business' => array(
        // 综合评价
        'merit' => array(
            'title' => '综合评价',
            'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score)),
        ),
        // 交易次数
        'totaltrade' => array(
            'title' => '交易次数',
            'value' => strval($user_result['seller_data']['bill_pay_num']), // $profile['review_times']), // 交易次数
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
        ),
    ),
);

$user_property = interface_get_seller_property($profile['att_data']);
foreach ($user_property as $val) {
    $u_type_id = $val['type_id'];
    $property['type_' . $u_type_id] = $val;
}
// 显示内容
$property['show'] = isset($property['type_' . $type_id]) ? 'type_' . $type_id : 'business';

$options['data'] = $property;
return $cp->output($options);

