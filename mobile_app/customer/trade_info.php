<?php

/**
 * 交易 详情
 *
 * @since 2015-6-29
 * @author chenweibiao <chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID

if (empty($user_id) || empty($order_sn)) {
    return $cp->output(array('data' => ''));
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
$order_info = $mall_order_obj->get_order_full_info($order_sn);
if (empty($order_info)) {
    return $cp->output(array('data' => ''));
}
if ($location_id == 'test') {
    $options['data'] = $order_info;
    return $cp->output($options);
}
$goods = $order_info['detail_list'][0];
// 组装交易详情数据
$trade_data = array(
    'order_sn' => $order_info['order_sn'], // 订单编号
    'status' => $order_info['status'], // 状态
    'status_str' => $order_info['status_str'], // 状态
    // 金额
    'cost' => array(
        'name' => '订单金额：',
        'value' => '￥' . $order_info['total_amount'],
    ),
    'remark' => array(
        'name' => '备注：',
        'value' => $order_info['description'],
    ),
    'goods' => array(
        'id' => $goods['goods_id'],
        'name' => $goods['goods_name'],
        'image' => $goods['goods_images'],
        'price' => '￥' . $goods['prices'], // amount
        'property' => array('name' => '规格：', 'value' => $goods['prices_spec'])
    ),
    // 商家
    'seller' => array(
        'name' => '商家：',
        'value' => get_seller_nickname_by_user_id($order_info['seller_user_id']),
        'request' => 'yueseller://goto?user_id=' . $user_id . '&seller_user_id=' . $order_info['seller_user_id'] . '&pid=1220103&type=inner_app',
    ),
    'trade' => array(
        array('title' => '订单号：', 'value' => $order_info['order_sn']),
//        array('title' => '下单时间：', 'value' => date('Y-m-d H:i', $order_info['add_time'])),
        array('title' => '成交时间：', 'value' => $order_info['is_pay'] == '1' ? date('Y-m-d H:i', $order_info['pay_time']) : date('Y-m-d H:i', $order_info['add_time'])),
    ),
    'action' => interface_trade_buyer_action($order_info['status'], $order_info['is_buyer_comment']),
);
// 查看 是否 商品 是否需要 点击跳转
if ($order_info['is_special'] == 1) {
    $trade_data['goods']['request'] = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods['goods_id'] . '&pid=1220111&type=inner_app';
}
$options['data'] = $trade_data;
return $cp->output($options);