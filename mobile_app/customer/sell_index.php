<?php
/**
 * 消费者首页
 *
 * @author willike
 * @since 2015/11/5
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$location_id = $client_data['data']['param']['location_id'];
$obj = POCO::singleton('pai_user_class');
$user_info = $obj->get_user_info($user_id);  // 获取用户 信息
if (empty($user_info)) {
    return $cp->output(array('data' => array()));
}
if ($location_id == 'test') {
    return $cp->output(array('data' => $user_info));
}

// 钱包
$bill_url = 'http://yp.yueus.com/mall/wallet/'; //'http://yp.yueus.com/mobile/m2/mine/bill/?type=trade';
$trade_url = 'http://yp.yueus.com/mall/user/test/order/list.php?type_id=0&status=0';  // 订单列表

$index_info = array(
    'mid' => '122LB08002',
    'user_id' => $user_id,
    'nickname' => $user_info['nickname'], // get_user_nickname_by_user_id($user_id),
    'icon' => get_user_icon($user_id, 165, TRUE),
    'location_id' => $user_info['location_id'],
    'city_name' => get_poco_location_name_by_location_id($user_info ['location_id']), // 所在城市
    // 签到券
    'code_url' => 'yueyue://goto?type=inner_app&pid=1220092&user_id=' . $user_id, //
    'order_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($trade_url) . '&wifi_url=' . urlencode($trade_url) . '&showtitle=0', // 订单链接

    'favor_url' => 'yueyue://goto?type=inner_app&pid=1220148&user_id=' . $user_id, // 收藏 1220148收藏-商家列表,1220149收藏-商品列表,1220150聊天页-商家收藏

    'wallet_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($bill_url) . '&wifi_url=' . urlencode($bill_url) .
        '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $bill_url)) . '&showtitle=1', // 账单列表
    'trade_url' => 'yueyue://goto?type=inner_app&pid=1220155&user_id=' . $user_id,  // 交易记录
    // 客服
    'service_url' => 'yueyue://goto?user_id=' . $user_id . '&receiver_id=10000&receiver_name=' .
        urlencode(mb_convert_encoding('约约客服', 'utf8', 'gbk')) . '&pid=1220021&type=inner_app',
    // 设置
    'setting_url' => 'yueyue://goto?type=inner_app&pid=1220043&user_id=' . $user_id, //
    // 一些 统计
    'code_num' => '0', // 签到券总数
    'order_num' => '0', // 我的订单 数
    'trade_num' => '',  // 交易记录

    'edit' => array(
        'title' => '更新资料',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&buyer_id=' . $user_id . '&pid=1220123&type=inner_app',
    ),
);

// 订单数
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_buyer($user_id, array('detail', 'activity'));
if ($num_result) {
    $index_info['order_num'] = strval(intval($num_result['wait_pay']) + intval($num_result['wait_sign']));
//    $index_info['trade_num'] = // TOOD:: 交易记录
}

// 签到券数量
$activity_obj = POCO::singleton('pai_activity_code_class');
$ticket_arr = $activity_obj->get_new_act_ticket($user_id, '0,1000');
$ticket_num = empty($ticket_arr) ? 0 : count($ticket_arr);
$index_info['code_num'] = strval($ticket_num);
if ($location_id == 'test1') {
    $options['data'] = array(
        '$num_result' => $num_result,
        '$ticket_num' => $ticket_num,
        '$ticket_arr' => $ticket_arr,
    );
    return $cp->output($options);
}
$options['data'] = $index_info;
return $cp->output($options);
