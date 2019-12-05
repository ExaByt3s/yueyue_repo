<?php

/**
 * 交易 详情
 * 
 * @since 2015-6-29
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID

if (empty($user_id) || empty($order_sn)) {
    $cp->output(array('data' => ''));
    exit;
}
$api_obj = POCO::singleton('pai_mall_api_class');   // 实例化商家交易对象
$order_info = $api_obj->api_get_order_full_info($order_sn);
if (empty($order_info)) {
    $cp->output(array('data' => ''));
    exit;
}
if ($location_id == 'test') {
    $options['data'] = $order_info;
    $cp->output($options);
    exit;
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
        'value' => get_user_nickname_by_user_id($order_info['seller_user_id']),
        'request' => 'yueseller://goto?user_id=' . $user_id . '&seller_user_id=' . $order_info['seller_user_id'] . '&pid=1220103&type=inner_app',
    ),
    'trade' => array(
        array('title' => '订单号：', 'value' => $order_info['order_sn']),
//        array('title' => '下单时间：', 'value' => date('Y-m-d H:i', $order_info['add_time'])),
        array('title' => '成交时间：', 'value' => $order_info['is_pay'] == '1' ? date('Y-m-d H:i', $order_info['pay_time']) : date('Y-m-d H:i', $order_info['add_time'])),
    ),
    'action' => btn_action($order_info['status'], $order_info['is_buyer_comment']),
);

$options['data'] = $trade_data;
$cp->output($options);

/**
 * 按钮 操作
 * 
 * @param string $status 交易状态
 * @param string $is_buyer_comment 买家是否评论
 * @return array 
 */
function btn_action($status, $is_buyer_comment) {
    if ($is_buyer_comment == 1) {  // 买家已评论
        return array();
    }
    // 按钮文案
    $action_arr = array(
        0 => '取消订单.close',
//        1 => '拒绝.refuse|同意.accept',
        2 => '申请退款.close|出示二维码.sign',
        7 => '删除订单.delete',
        8 => '评价对方.appraise'
    );
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}
