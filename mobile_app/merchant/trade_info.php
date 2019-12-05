<?php

/**
 * 交易 详情
 *
 * @since 2015-6-17
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID

if (empty($user_id) || empty($order_sn)) {
    return $cp->output(array('data' => array()));
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
$order_info = $mall_order_obj->get_order_full_info($order_sn);
if (empty($order_info)) {
    return $cp->output(array('data' => array()));
}
if ($location_id == 'test') {
    $options['data'] = $order_info;
    return $cp->output($options);
}
$type_id = $order_info['type_id'];  // 大分类ID
$status = $order_info['status'];
$is_seller_comment = $order_info['is_seller_comment'];  // 是否评价
$status_str = $order_info['status_str'];
if ($status == 8 && $is_seller_comment == 0) {  // 未评价
    $status = 10;
    $status_str = '待评价';
}
$goods_list = $type_id == 42 ? $order_info['activity_list'] : $order_info['detail_list'];  // 商品详情
$goods_info = $goods_list[0];  // 商品信息, 取第一个
// 商品ID/活动ID
$goods_id = $type_id == 42 ? $goods_info['activity_id'] : $goods_info['goods_id'];
// 组装交易详情数据
$property_data = interface_trade_property_data($goods_info, $version);
$buyer_user_id = $order_info['buyer_user_id'];  // 买家ID
$buyer_user_name = empty($order_info['buyer_name']) ? get_user_nickname_by_user_id($buyer_user_id) : $order_info['buyer_name'];    // 买家名称
$original_amount_str = $order_info['original_amount_str'];  // 原价/促销价/改价之类后 的显示
$original_amount_str = (empty($original_amount_str) || $original_amount_str == '()') ? '' : $original_amount_str;
$trade_data = array(
    'title' => $type_id == 42 ? '活动订单详情' : '服务订单详情',
    'order_sn' => $order_info['order_sn'], // 订单编号
    'status' => $status, // 状态
    'status_str' => $status_str, // 状态
    'notice' => '备注：' . (empty($order_info['description']) ? '--' : $order_info['description']), // 备注
    'remark' => array('title' => '备注：', 'value' => $order_info['description']), // 备注 for 1.3.0
//    'expire_time' => $order_info['expire_time'], // 订单过期时间(自行计算有效时间)
    'expire' => $order_info['expire_str'], // 订单过期时间
    // 买家
    'customer' => array(
        'title' => '联系买家',
        'user_id' => $buyer_user_id,
        'name' => '买家：',
        'value' => $buyer_user_name,
        'avatar' => $order_info['buyer_icon'],  // 消费者头像
        'request' => 'yueseller://goto?user_id=' . $buyer_user_id . '&buyer_id=' . $buyer_user_id . '&pid=1250027&type=inner_app', // 买家页面
        'consult' => 'yueseller://goto?user_id=' . $user_id . '&receiver_id=' . $buyer_user_id .
            '&receiver_name=' . urlencode(mb_convert_encoding($buyer_user_name, 'utf-8', 'gbk')) . '&pid=1250025&type=inner_app', // 聊天界面
    ),
    // 金额
    'cost' => array(
        'name' => '总价：',
        'value' => '￥' . sprintf('%.2f', $order_info['total_amount']),
        'total' => sprintf('%.2f', $order_info['total_amount']),  // 总金额（订单改价后的金额）
        'promotion' => 0.00,  // 促销价格
        'original' => sprintf('%.2f', $order_info['original_amount']), // 促销后的订单金额（订单改价前的金额） $order_info['original_subtotal']), // 最原始价格
        'original_str' => $original_amount_str,
//        'initial' => sprintf('%.2f', $order_info['original_subtotal']),  // 最初价格
    ),
    'bill' => array(
        array('title' => '订单金额：', 'value' => '￥' . $order_info['total_amount']),
        array('title' => '优惠券', 'value' => '-￥' . $order_info['discount_amount']),
        array('title' => '实付款', 'value' => '￥' . $order_info['pending_amount']),
    ),
    'showing' => $property_data['showing'],  // 活动
    'goods' => $property_data['goods'],  // 商品信息
    'detail' => $property_data['detail'],  // 订单详情
    'property' => $property_data['property'],  // 订单属性
    'trade' => array(
        array('title' => '订单编号：', 'value' => $order_info['order_sn']),
        array('title' => '下单时间：', 'value' => date('Y-m-d H:i', $order_info['add_time'])),
        array('title' => '支付时间：', 'value' => $order_info['is_pay'] == '1' ? date('Y-m-d H:i', $order_info['pay_time']) : '--'),
        array('title' => '客服电话：', 'value' => '4000-82-9003'),
    ),
    'action' => btn_action($order_info['status'], $is_seller_comment, $version),
);
// 查看商品 是否需要 点击跳转
if ($order_info['is_special'] != 1) {
    // 非特殊商品, 附加链接 (可跳转 )
    if ($type_id == 42) {  // 活动订单
        // 活动详情
        $trade_data['goods']['request'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
    }else{
        $trade_data['goods']['request'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250007&type=inner_app';
    }
}
// 是否促销
if ($goods_info['is_goods_promotion'] == 1 || $goods_info['is_activity_promotion'] == 1) {
    // 参加促销
    $total_amount = sprintf('%.2f', $order_info['total_amount']);
    if (version_compare($version, '1.3', '<') && !empty($original_amount_str)) {
        $trade_data['cost']['value'] = '￥' . $total_amount . '(' . $original_amount_str . ')';
    }
    $trade_data['cost']['promotion'] = $goods_info['goods_promotion_amount']; // 促销价格
    // 促销信息
    $promotion_info = $type_id == 42 ? $order_info['activity_promotion_info'] : $order_info['goods_promotion_info'];
    $period = str_replace('-', '.', $promotion_info['start_time']) . '-' . str_replace('-', '.', $promotion_info['end_time']);
    $trade_data['promotion'] = array(
        'abate' => '最高可省：' . $promotion_info['cal_used_amount'],
        'notice' => $promotion_info['type_name'],
        'period' => $period,
        'description' => $period . "\n" . $promotion_info['promotion_desc'],
        'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
    );
}
// 是否改价
if ($order_info['is_change_price'] == 1) {
    // original_subtotal最原始价格，pending_amount实际支付价格，discount_amount优惠券价格，total_amount总金额（订单改价后的金额）,original_amount促销后的订单金额（订单改价前的金额）
    $trade_data['promo'] = '改价原因：' . $order_info['change_price_reason'];
    $original_amount = $order_info['original_subtotal']; // $order_info['original_amount'];
    $original_amount = ($original_amount == intval($original_amount)) ? intval($original_amount) : sprintf('%.2f', $original_amount);
    $total_amount = sprintf('%.2f', $order_info['total_amount']);
//    $total_amount = ($total_amount == intval($total_amount)) ? intval($total_amount) : sprintf('%.2f', $total_amount);
    if (version_compare($version, '1.3', '<') && !empty($original_amount_str)) {
        $trade_data['cost']['value'] = '￥' . $total_amount . '(' . $original_amount_str . ')';
    }
    $trade_data['cost']['total'] = strval($total_amount);
}
// 订单流程
$trade_process = array();
foreach ($order_info['process_list'] as $process) {
    $trade_process[] = array(
        'notice' => $process['process_content'],
        'name' => $process['process_nickname'],
        'avatar' => $process['process_icon'],
        'time' => date('Y-m-d H:i', $process['process_time']),
        'reason' => '',  // 预留字段,有则出现
    );
}
$trade_data['process'] = $trade_process;

/**
 * 按钮 操作
 *
 * @param string $status 交易状态
 * @param string $is_seller_comment 商家评论
 * @param string $version
 * @return array
 */
function btn_action($status, $is_seller_comment, $version) {
    if ($is_seller_comment == 1) {  // 商家已评论
        return array();
    }
    // 按钮文案
    $action_arr = array(
        0 => '关闭订单.close',
        1 => '拒绝订单.refuse|接受订单.accept',
        2 => '扫码签到.sign',
//        2 => '取消交易并退款.close|扫码签到.sign',
//        7 => '删除订单.delete',
        8 => '评价对方.appraise'
    );
    if (version_compare($version, '1.1') >= 0) {
        $action_arr[0] = '关闭订单.close|修改价格.pending';
    }
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

$options['data'] = $trade_data;
return $cp->output($options);
