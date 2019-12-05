<?php
/**
 * 活动订单 列表
 *
 * @author willike
 * @since 2015/11/10
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 全部 all 待付款 unpaid 待确认 tbc 待签到 checkin 已完成  completed 已关闭 closed
$trade_type = $client_data['data']['param']['trade_type'];   // 交易类型
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
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$trade_data = $mall_order_obj->get_activity_list_by_order_for_seller($user_id, $status, false, 'lately_time DESC', $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$status' => $status,
        '$limit_str' => $limit_str,
        '$trade_data' => $trade_data,
    );  // for test
    return $cp->output($options);
}
$activity_message_obj = POCO::singleton('pai_mall_activity_batch_message_class');
$trade_list = array();
foreach ($trade_data as $value) {
    $activity_id = $value['activity_id']; // 活动ID
    $stage_id = $value['stage_id'];  // 场次ID
    $times_result = $activity_message_obj->send_times_left($user_id, $activity_id, $stage_id);
    $send_times = intval($times_result); // 群发次数
    $stage_min_price = $value['stage_min_price'];  // 最低价
    $stage_max_price = $value['stage_max_price'];  // 最高价
    $show_price = '￥' . ($stage_min_price > 0 ? $stage_min_price . '-' : '') . ($stage_max_price > 0 ? $stage_max_price : '');  // 显示金额
    $trade_info = array(
        'goods_id' => $activity_id, // 活动ID
        'stage_id' => $stage_id,  // 场次ID
        'title' => $value['activity_name'], // 活动名称
        'desc' => date('Y-m-d', $value['service_start_time']) . ' ' . $value['stage_title'], // 场次名称
        'price_str' => $show_price, // 用于显示的价格
        'attend' => '已付款：' . $value['attend_num'] . '/' . $value['stock_num_total'] . '人',
        'thumb' => $value['activity_images'], // 预览图
        'link' => 'yueseller://goto?user_id=' . $user_id . '&activity_id=' . $value['activity_id'] . '&stage_id=' . $value['stage_id'] . '&pid=1250043&type=inner_app', // 跳转订单列表
        'send_link' => 'yueseller://goto?user_id=' . $user_id . '&activity_id=' . $value['activity_id'] .
            '&stage_id=' . $value['stage_id'] . '&send_times=' . $send_times . '&max_len=200' . '&pid=1250046&type=inner_app', // 跳转订单详情
        'action' => array(
            array('title' => '群发通知', 'request' => 'mass',),
        ),
    );
    $trade_list[] = $trade_info;
}
$options['data'] = array(
    'list' => $trade_list,
);
return $cp->output($options);