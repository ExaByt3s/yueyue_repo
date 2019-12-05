<?php

/**
 * 商家 页面
 * 
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // 商家ID
$version = $client_data['data']['version'];  // 版本

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->show_seller_data_for_temp($seller_user_id);  // 获取商家用户信息
if (empty($user_result)) {
    $options['data'] = array();
    return $cp->output($options);
}
if ($location_id == 'test') {
    $options['data'] = array(
        '$version' => $version,
        '$user_result' => $user_result,
    );
    return $cp->output($options);
}
//$user = $user_result['seller_data'];
$profile = $user_result['seller_data']['profile'][0];
$type_id_arr = explode(',', $profile['type_id']);  // 用户认证   3 化妆,31 模特,40 摄影师,12 影棚,5 培训
$profile_info = array();  // 简介
foreach ($profile['default_data'] as $value) {
    $profile_info[$value['key']] = $value['value'];
}
$introduce = interface_content_strip($profile_info['introduce']);  // 个人介绍
$introduce_more = (strrpos($introduce, '...', -3) === false) ? 0 : 1; // 是否有更多简介
// 综合评分
$score = $profile['average_score'];
$score = intval($score) <= 0 ? 5 : $score;
$location = get_poco_location_name_by_location_id($profile_info['location_id']);
$user_info = array(
    'user_id' => $profile['user_id'], // 用户ID
    'cover' => yueyue_resize_act_img_url($profile['cover'], '640'), // 背景图
    'avatar' => get_seller_user_icon($profile['user_id']), // $profile['avatar'], // 头像
    'name' => $profile['name'],
    'type_id' => $profile['type_id'], // 认证
//    'sex' => $profile['sex'],
    'introduce' => trim($introduce),
    'introduce_more' => $introduce_more,
    'detail' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220111&type=inner_app', // 资料详情
    'favor' => array(
        'title' => '收藏',
        'value' => '0', // 1 已收藏
    ),
    'location' => $location . '    ID: ' . $profile['user_id'],
    // 属性
    'property' => array(),
    // 交易信息
    'business' => array(
        'merit' => array('title' => '综合评价', 'value' => strval($score > 5 ? 5 : ( $score < 0 ? 0 : $score))), // 综合评价
        'totaltrade' => array('title' => '交易次数', 'value' => strval($user_result['seller_data']['bill_pay_num'])), // 交易次数
        // 'totaltrade' => array('title' => '交易次数', 'value' => $profile['review_times']), // 交易次数
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
    ),
);

// 3 化妆,31 模特,40 摄影师,12 影棚,5 培训
$user_info['property'] = interface_get_seller_property($profile['att_data']);

$options['data'] = $user_info;
return $cp->output($options);

