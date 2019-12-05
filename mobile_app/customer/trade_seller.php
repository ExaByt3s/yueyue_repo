<?php

/**
 * 商家主页 页面
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-6-19
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // 商家ID
$version = $client_data['data']['version'];  // 版本
$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($seller_user_id, 2);  // 获取商家用户信息
if (empty($user_result)) {
    // 商家获取不到数据, 则使用消费者数据
    $seller_obj = POCO::singleton('pai_user_class');
    $user_result = $seller_obj->get_user_info($seller_user_id);  // 获取消费者用户信息
    $introduce = interface_content_strip($user_result['remark']);  // 个人介绍
    // 获取 交易次数和消费金额
    $obj = POCO::singleton('pai_user_data_class');
    $record_result = $obj->get_user_data_info($seller_user_id);
    $score = $record_result['comment_score'];   // 用户评价
    $score = empty($score) ? 5 : $score;  // 默认5分
    $avatar = get_user_icon($user_result['user_id']);
    // 地理位置
    if (version_compare($version, '3.3', '>')) {
        $location = get_poco_location_name_by_location_id($user_result['location_id']);
    } else {
        $location = get_poco_location_name_by_location_id($user_result['location_id']) . '    ID: ' . $user_result['user_id'];
    }
    $user_info = array(
        'user_id' => $user_result['user_id'], // 用户ID
        'cover' => yueyue_resize_act_img_url($avatar, '640'), // 背景图
        'avatar' => $avatar, // 头像
        'name' => $user_result['nickname'], // 昵称
        'introduce' => trim($introduce),
        'detail' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220111&type=inner_app', // 资料详情
        'character' => 'ID: ' . $user_result['user_id'],
        'location' => $location,
        // 属性
        'property' => array(),
        // 交易信息
        'business' => array(
            'merit' => array('title' => '综合评价', 'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score))), // 综合评价
            'totaltrade' => array('title' => '交易次数', 'value' => strval($record_result['deal_times'])), // 交易次数
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
        ),
        'showtitle' => '精彩图集',
        // 作品展示
        'showcase' => array(),
        // 分享
        'share' => $seller_obj->get_share_text($seller_user_id),
    );
    $options['data'] = $user_info;
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
$favor = array(
    'title' => '收藏',
    'value' => '0', // 1 已收藏
);
if (version_compare($version, '3.2', '>')) {  // 3.2.0 开启收藏
    $follow_user_obj = POCO::singleton('pai_mall_follow_user_class');
    $favor_res = $follow_user_obj->check_user_follow($user_id, $seller_user_id);
    if (true == $favor_res) {
        $favor = array(
            'title' => '已收藏',
            'value' => '1', // 1 已收藏
        );
    }
}
// 综合评分
$score = $profile['average_score'];
$score = intval($score) <= 0 ? 5 : $score;
// 地理位置
if (version_compare($version, '3.3', '>')) {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']);
} else {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']) . '    ID: ' . $profile['user_id'];
}
// 交易次数
$trade_num = $user_result['seller_data']['bill_finish_num']; //$user_result['seller_data']['bill_pay_num']; // 交易次数
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
    'favor' => $favor, // 收藏
    'character' => 'ID: ' . $profile['user_id'],
    'location' => str_replace(' ', '·', $location),
    // 属性
    'property' => array(),
    // 交易信息
    'business' => array(
        'merit' => array('title' => '综合评价', 'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score))), // 综合评价
        'totaltrade' => array('title' => '交易次数', 'value' => $trade_num . '次'), // 交易次数
        // 'totaltrade' => array('title' => '交易次数', 'value' => $profile['review_times']), // 交易次数
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
    ),
    'showtitle' => '服务列表',
    // 作品展示
    'showcase' => array(),
    // 更多商品
    'morecase' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220109&goods_type=all&type=inner_app',
    // 分享
    'share' => array(),
    // 咨询
    'consult_link' => 'yueyue://goto?user_id=' . $user_id . '&receiver_id=' . $profile['user_id'] . '&receiver_name=' .
        urlencode(mb_convert_encoding($profile['name'], 'utf8', 'gbk')) . '&pid=1220021&type=inner_app',
);

// 获取用户分享信息
$share_result = $seller_obj->get_share_text($seller_user_id);
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
$goods_list = $task_goods_obj->user_goods_list($seller_user_id, $data, false, 'goods_id DESC', '0,6', '*');
if (version_compare($version, '3.3', '>')) {
    // 活动信息
    $data = array(
        'action_type' => 1,
    );
    $event_list = $task_goods_obj->user_goods_list($seller_user_id, $data, false, 'goods_id DESC', '0,6', '*');
    if ($location_id == 'test2') {
        $options['data'] = array(
            '$seller_user_id' => $seller_user_id,
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
if (count($goods_list) < 6 && version_compare($version, '3.1') >= 0) {
    // 是否显示 查看更多 链接
    unset($user_info['morecase']);
}
$promotion_obj = POCO::singleton('pai_promotion_class');  // 促销
foreach ($goods_list as $value) {
    $type_id = $value['type_id'];  // 大分类
    $price_str = $value['prices'];
    $prices_list = unserialize($value['prices_list']);
    $pro_prices_list = array();
    if (!empty($prices_list)) {
        $tmp = 0;
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
    } else {
        $pro_prices_list = $price_str;  // 单个价格(for 促销)
    }
    $goods_id = $value['goods_id']; // 商品ID
    $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app'; // 服务详情
    if ($type_id == 42) {
        // 活动详情
        $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&goods_id=' . $goods_id . '&pid=1220152&type=inner_app';
    }
    $case_info = array(
        'goods_id' => $goods_id,
        'title' => $value['titles'],
        'prices' => '￥' . $price_str,
        'pic' => $value['images'],
        'link' => $link,   // 服务详情
//        'content' => $value['content'],
    );
    if (!empty($pro_prices_list) && version_compare($version, '3.2', '>') && $type_id != 42) {
        $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '立省: ' . $promotion['abate'];
            // 添加 促销信息
            $case_info = array_merge($case_info, $promotion);
        }
    }
    if (version_compare($version, '3.3', '<')) {
        $user_info['showcase'][] = $case_info;
        continue;
    }
    $add_time = $value['add_time'];  // 添加时间
    $showcase[$add_time] = $case_info;
}
if (version_compare($version, '3.3', '>')) {
    krsort($showcase);
    $user_info['showcase'] = array_slice($showcase, 0, 6);
}

$options['data'] = $user_info;
return $cp->output($options);

