<?php

/**
 * 交易 列表
 *
 * @since 2015-6-17
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 全部 all 待付款 unpaid 待确认 tbc 待签到 checkin 已完成  completed 已关闭 closed
$trade_type = $client_data['data']['param']['trade_type'];   // 交易类型
$type_id = intval($client_data['data']['param']['type_id']);   // 商品类型
$version = $client_data['data']['version'];  // 版本号
if (empty($user_id)) {
    $options['data']['list'] = array();
    return $cp->output($options);
}
$limit = trim($client_data['data']['param']['limit']);  // 传值如: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // 第几页
    $rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}
// 待付款 0, 待确认 1, 待签到 2, 已关闭 7, 已完成 8, 所有 -1 
switch ($trade_type) {
    case 'unpaid':  // 待付款
        $status = 0;
        break;
    case 'tbc':  // 待确认
        $status = 1;
        break;
    case 'checkin':  // 待签到
        $status = 2;
        break;
    case 'completed':  // 已完成
        $status = 8;
        break;
    case 'closed':  // 已关闭
        $status = 7;
        break;
    default:  // 所有
        $status = -1;
        break;
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
$trade_data = $mall_order_obj->get_order_list_by_detail_for_seller($user_id, $type_id, $status, false, 'lately_time DESC', $limit_str, '*');
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$type_id' => $type_id,
        '$status' => $status,
        '$limit_str' => $limit_str,
        '$trade_data' => $trade_data,
    );  // for test
    return $cp->output($options);
}
$trade_list = array();
foreach ($trade_data as $value) {
    $order_sn = $value['order_sn'];
    $status = $value['status'];
    $is_seller_comment = $value['is_seller_comment'];  // 是否评价
    $status_str = $value['status_str'];
    if ($status == 8 && $is_seller_comment == 0) {  // 未评价
        $status = 10;
        $status_str = '待评价';
    }
    $show_price = '￥' . $value['total_amount'];  // 显示金额
    $original_amount = $value['original_amount']; // 促销后价格 $value['original_subtotal']; // 最原始价格
    if ($value['is_change_price'] == 1 || $value['detail_list'][0]['is_goods_promotion'] == 1) {
        // 改价 or 促销
        $show_price = '￥' . $value['total_amount'] . '(' . $value['original_amount_str'] . ')';
    }
    $buyer_user_id = $value['buyer_user_id'];
    $trade_info = array(
        'order_sn' => $order_sn,
        'type_id' => $value['type_id'], // 商品品类
        'title' => get_user_nickname_by_user_id($buyer_user_id), // 标题, 消费者
        'desc' => $value['detail_list'][0]['goods_name'], // 描述, 可能是商品名
        'link' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $order_sn . '&pid=1250022&type=inner_app', // 跳转订单详情
        'status' => $status, // 状态
        'status_str' => $status_str, // 状态名称
        'price_str' => $show_price, // 用于显示的价格
        'cost' => $value['total_amount'], // 金额
        'original' => $original_amount, // 原价金额
        'goods_id' => $value['detail_list'][0]['goods_id'], // 商品ID
        'thumb' => $value['detail_list'][0]['goods_images'], // 预览图
        'action' => interface_trade_seller_action($value['status'], $value['is_seller_comment'], $version),
    );
    $trade_list[] = $trade_info;
}

$options['data']['list'] = $trade_list;
return $cp->output($options);