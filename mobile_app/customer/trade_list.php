<?php

/**
 * 交易记录 列表
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-7-1
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
//require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 全部 all 待付款 unpaid 待确认 tbc 待签到 checkin 已完成  completed 已关闭 closed
$trade_type = $client_data['data']['param']['trade_type'];   // 交易类型
$type_id = $client_data['data']['param']['type_id'];   // 商品类型
$type_id = intval($type_id); // 某个品类/0全部
if (empty($user_id)) {
    $options['data'] = array(
        'result' => 0,
        'message' => '用户ID为空',
        'list' => array(),
    );
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
$trade_data = $mall_order_obj->get_order_list_for_buyer($user_id, $type_id, $status, false, 'order_id DESC', $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$type_id' => $type_id,
        '$status' => $status,
        '$trade_data' => $trade_data,
    );
    return $cp->output($options);
}
$trade_total = 0;
if (!empty($trade_data)) {
    // 总数
    $trade_total = $mall_order_obj->get_order_list_for_buyer($user_id, $type_id, $status, true);
}
$trade_list = array();
foreach ($trade_data as $value) {
    $order_sn = $value['order_sn'];
    $status = $value['status'];
    $type_id = $value['type_id'];  // 分类ID
    $is_buyer_comment = $value['is_buyer_comment'];  // 是否评价
    $status_str = $value['status_str'];
    $appraise_link = '';
    if ($status == 8 && $is_buyer_comment == 0) {  // 未评价
        $status = 10;
        $status_str = '未评价';
        $appraise_url = 'http://yp.yueus.com/mall/user/comment/?order_sn=' . $order_sn . '&stage_id=';
        $appraise_link = 'yueyue://goto?type=inner_web&url=' . urlencode($appraise_url) . '&wifi_url=' . urlencode($appraise_url) . '&showtitle=1';
    }
    // 详情
    $detail_url = 'http://yp.yueus.com/mall/user/test/order/detail.php?order_sn=' . $order_sn;
    $detail_link = 'yueyue://goto?type=inner_web&url=' . urlencode($detail_url) . '&wifi_url=' . urlencode($detail_url) . '&showtitle=1';

    $seller_user_id = $value['seller_user_id'];
    $goods_id = $value['detail_list'][0]['goods_id']; // 商品ID

    $title = $value['detail_list'][0]['goods_name'];// 商品名称
    $service_time = $value['detail_list'][0]['service_time'];  // 服务时间
    $cover = $value['detail_list'][0]['goods_images']; // 封面
    if ($type_id == 42) {  // 活动
        $goods_id = $value['activity_list'][0]['activity_id']; // 活动ID
        $title = $value['activity_list'][0]['activity_name']; // 活动名
        $service_time = $value['activity_list'][0]['service_start_time'];  // 服务时间
        $cover = $value['activity_list'][0]['activity_images'];
    } elseif ($type_id == 20) { // 面付
        $goods_id = '';
        $title = '直接支付';  // 名称
        $service_time = $value['pay_time'];  // 支付时间
        $cover = $value['seller_icon'];
    }
    $service_time = (empty($service_time) || $service_time == '--') ? '--' : date('Y.m.d', $service_time);
    $trade_info = array(
        'order_sn' => $order_sn,
        'type_id' => $value['type_id'], // 品类
        'seller' => array(
            'user_id' => $value['seller_user_id'],
            'name' => $value['seller_name'],
            'avatar' => $value['seller_icon'],
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $value['seller_user_id'] . '&pid=1220103&type=inner_app',
        ),
        'title' => $title, // 服务名称
        'goods_id' => $goods_id, // 商品ID
        'cover' => $type_id == 20 ? $cover : yueyue_resize_act_img_url($cover, 260), // 预览图
        'status' => $status, // 状态
        'status_str' => $status_str, // 状态名称
        'cost' => '￥' . $value['total_amount'], // 金额
        'order_time' => '下单时间：' . date('Y.m.d', $value['add_time']),
        'service_time' => '服务时间：' . $service_time,
        'link' => $detail_link, // 跳转 订单详情
        'appraise_link' => $appraise_link,  // 评价
    );
    $trade_list[] = $trade_info;
}

$options['data'] = array(
    'total' => $trade_total,
    'list' => $trade_list,
);
return $cp->output($options);
