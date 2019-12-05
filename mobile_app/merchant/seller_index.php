<?php

/**
 * 商家主页
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // 获取用户信息
if (empty($user_result)) {  // 无数据
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
// 地理位置
if (version_compare($version, '1.3', '>')) {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']);
} else {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']) . '    ID: ' . $profile['user_id'];
}
// 综合评分
$score = $profile['average_score'];
$score = intval($score) <= 0 ? 5 : $score;
$user_info = array(
    'user_id' => $profile['user_id'], // 用户ID
    'cover' => yueyue_resize_act_img_url($profile['cover'], '640'), // 背景图
    'avatar' => get_seller_user_icon($profile['user_id'], 165, TRUE), // $profile['avatar'], // 头像
    'name' => $profile['name'],
    'type_id' => $profile['type_id'], // 认证
//    'sex' => $profile['sex'],
    'introduce' => trim($introduce),
    'detail' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250005&type=inner_app', // 资料详情
    'character' => 'ID: ' . $profile['user_id'],
    'location' => str_replace(' ', '·', $location),
    // 属性
    'property' => array(),
    // 交易信息
    'business' => array(
        'merit' => array('title' => '综合评价', 'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score))), // 综合评价
        // 交易次数
        'totaltrade' => array(
            'title' => '交易次数',
            'value' => $user_result['seller_data']['bill_pay_num'] . '次', // 完成交易数 ( $profile['review_times'] 评价次数 )
            'request' => 'yueseller://goto?user_id=' . $profile['user_id'] . '&pid=1250009&type=inner_app', // 用户评价列表
        ),
    ),
    'showtitle' => '服务列表',
    // 作品展示
    'showcase' => array(),
    // 更多商品
    'morecase' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250026&goods_type=all&type=inner_app',
    // 分享
    'share' => array(),
);

// 获取用户分享信息
$share_result = $seller_obj->get_share_text($user_id);
$user_info['share'] = $share_result;
// 3 化妆,31 模特,40 摄影师,12 影棚,5 培训
$user_info['property'] = interface_get_seller_property($profile['att_data']);
// 商品列表
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
// status状态 0未审核,1通过,2未通过,3删除;show上/下架 1上架,2下架;type_id商品类型,keyword搜索关键字
$data = array(
    'status' => 1,
    'show' => 1,
    'type_id' => 0,
    'keyword' => '',
);
$goods_list = $task_goods_obj->user_goods_list($user_id, $data, false, 'goods_id DESC', '0,6', '*');
if (version_compare($version, '3.3', '>')) {
    // 活动信息
    $data = array(
        'action_type' => 1,
    );
    $event_list = $task_goods_obj->user_goods_list($user_id, $data, false, 'goods_id DESC', '0,6', '*');
    if ($location_id == 'test2') {
        $options['data'] = array(
            '$user_id' => $user_id,
            '$data' => $data,
            '$event_list' => $event_list,
        );
        return $cp->output($options);
    }
    $goods_list = array_merge($goods_list, $event_list);
}
if ($location_id == 'test1') {
    $options['data'] = array(
        '$seller_user_id' => $seller_user_id,
        '$data' => $data,
        '$goods_list' => $goods_list,
    );
    return $cp->output($options);
}
if (count($goods_list) < 6 && version_compare($version, '1.1') >= 0) {
    // 是否显示 查看更多 链接
    unset($user_info['morecase']);
}
$promotion_obj = POCO::singleton('pai_promotion_class');  // 促销
$showcase = array();
foreach ($goods_list as $value) {
    $type_id = $value['type_id'];  // 大分类
    $price_str = $value['prices'];
    $prices_list = unserialize($value['prices_list']);
    $pro_prices_list = $price_str;  // 单个价格(for 促销)
    if (!empty($prices_list)) {
        $tmp = 0;
        $pro_prices_list = array();
        foreach ($prices_list as $k => $price) {
            if (intval($price) <= 0) {
                continue;
            }
            $pro_prices_list[] = array(
                'prices_type_id' => $k, //必填
                'goods_prices' => $price, //必填
            );
            if ($tmp > 0 && $price > $tmp) {
                continue;
            }
            $tmp = $price;
        }
        if ($tmp > 0) {
            $price_str = sprintf('%.2f', $tmp) . '元 起';
        }
    }
    $goods_id = $value['goods_id']; // 商品ID
    $link = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250007&type=inner_app'; // 服务详情
    if ($type_id == 42) {
        // 活动详情
        $link = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
    }
    $case_info = array(
        'goods_id' => $value['goods_id'],
        'type_id' => $type_id,
        'title' => $value['titles'],
        'prices' => '￥' . $price_str,
        'pic' => $value['images'],
        'link' => $link, // 服务详情
    );
    if (!empty($pro_prices_list) && version_compare($version, '1.2', '>')) {
        $promotion = interface_get_goods_promotion($user_id, $value['goods_id'], $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '立省: ' . $promotion['abate'];
            // 添加 促销信息
            $case_info = array_merge($case_info, $promotion);
        }
    }
    if (version_compare($version, '1.3', '<')) {
        $user_info['showcase'][] = $case_info;
        continue;
    }
    $add_time = $value['add_time'];  // 添加时间
    $showcase[$add_time] = $case_info;
}
if (version_compare($version, '1.3', '>')) {
    krsort($showcase);
    $user_info['showcase'] = array_slice($showcase, 0, 6);
}

$options['data'] = $user_info;
return $cp->output($options);

