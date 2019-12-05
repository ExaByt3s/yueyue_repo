<?php

/**
 * 买家 用户首页
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-16
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$buyer_id = $client_data['data']['param']['buyer_id'];  // 其他用户 ID
$is_mine = (empty($buyer_id) || $user_id == $buyer_id) ? TRUE : FALSE;
$buyer_id = empty($buyer_id) ? $user_id : $buyer_id;

$seller_obj = POCO::singleton('pai_user_class');
$user_result = $seller_obj->get_user_info($buyer_id);  // 获取用户信息
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$introduce = interface_content_strip($user_result['remark'], 300);  // 个人介绍
$is_display_record = intval($user_result['is_display_record']);  // 是否显示 消费记录
// 获取 交易次数和消费金额
$obj = POCO::singleton('pai_user_data_class');
$record_result = $obj->get_user_data_info($buyer_id);
if ($location_id == 'test1') {
    $options['data'] = $record_result;
    return $cp->output($options);
}
$score = $record_result['comment_score'];   // 用户评价
$score = empty($score) ? 5 : $score;  // 默认5分
$deal_times = $record_result['deal_times'];   // 交易次数
$consume_ammount = $record_result['consume_ammount'];   // 交易金额
$name = $user_result['nickname'];  // 昵称
$user_info = array(
    'user_id' => $user_result['user_id'], // 用户ID
    'name' => $name,
    'avatar' => get_user_icon($user_result['user_id'], 165, $is_mine), // 头像
    'location' => get_poco_location_name_by_location_id($user_result['location_id']),
    'introduce' => trim($introduce),
    // 聊天页面
    'chat' => array(
        'title' => '私聊TA',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&receiver_id=' . $user_result['user_id'] .
            '&receiver_name=' . urlencode(mb_convert_encoding($name, 'utf8', 'gbk')) . '&pid=1220021&type=inner_app',
    ),
    // 属性
    'property' => array(
//        array('title' => '会员等级', 'value' => '13'),   // 会员等级暂时隐藏 2015-7-22 @荣少
        array('title' => '交易次数', 'value' => strval($deal_times)),
        array('title' => '消费金额', 'value' => strval($consume_ammount)),
    ),
    // 消费记录
    'record_title' => '消费记录',
    'is_display_record' => strval($is_display_record),
    'record' => array(),
    // 交易信息
    'business' => array(
        'title' => '评价',
        'score' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score)),
        'merit' => array(
            'title' => '综合详情',
            'request' => 'yueyue://goto?user_id=' . $user_id . '&buyer_id=' . $user_result['user_id'] . '&pid=1220075&type=inner_app',
        ),
    ),
    'showtitle' => '精彩图集',
    // 作品展示
    'showcase' => array(),
    // 分享
//    'share' => array(),
);

// 获取 图集
$pic_obj = POCO::singleton('pai_pic_class');
$pic_list = $pic_obj->get_user_pic($buyer_id);
if ($location_id == 'test2') {
    $options['data'] = $pic_list;
    return $cp->output($options);
}
foreach ($pic_list as $value) {
    $img_url = $value['img'];
    $showcase = array(
        'thumb' => yueyue_resize_act_img_url($img_url, '440'), // 缩略图
        'original' => yueyue_resize_act_img_url($img_url), // 原图
    );
    $user_info['showcase'][] = $showcase;
}
if ($is_display_record === 1) {
    // 获取 消费记录
    $mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
    $trade_data = $mall_order_obj->get_order_list_for_buyer($buyer_id, 0, 8, false, 'order_id DESC', '0,6', '*');
    if ($location_id == 'test3') {
        $options['data'] = $trade_data;
        return $cp->output($options);
    }
    $trade_list = array();
    foreach ($trade_data as $value) {
        $type_id = $value['type_id'];  // 大分类ID
        $order_sn = $value['order_sn'];
        if ($type_id == 20) {  // 面付
            $goods_id = '';
            $cover = yueyue_resize_act_img_url($value['seller_icon'], 64);
            // 订单详情
            $detail_url = 'http://yp.yueus.com/mall/user/test/order/detail.php?order_sn=' . $order_sn;
            $link = 'yueyue://goto?type=inner_web&url=' . urlencode($detail_url) . '&wifi_url=' . urlencode($detail_url) . '&showtitle=1';
        } else if ($type_id == 42) {  // 活动
            $goods_id = $value['activity_list'][0]['activity_id'];
            $cover = yueyue_resize_act_img_url($value['activity_list'][0]['activity_images'], 145);
            $pid = 1220152;  // 活动详情
            $link = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=' . $pid . '&type=inner_app'; // 跳转活动页面
        } else {
            $goods_id = $value['detail_list'][0]['goods_id'];
            $cover = yueyue_resize_act_img_url($value['detail_list'][0]['goods_images'], 260);
            $pid = 1220102;
            $link = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=' . $pid . '&type=inner_app'; // 跳转商品页面
        }
        $trade_info = array(
            'order_sn' => $order_sn, // 订单号
            'goods_id' => $goods_id, // 商品ID
            'thumb' => $cover, // 预览图
            'link' => $link,
        );
        $user_info['record'][] = $trade_info;
    }
}

if ($is_mine === TRUE) {
    // 编辑用户
    $user_info['edit'] = array(
        'title' => '编辑',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220127&type=inner_app',
    );
    // 获取用户分享信息
    $user_info['share'] = $seller_obj->get_share_text($user_id);
}

$options['data'] = $user_info;
return $cp->output($options);

