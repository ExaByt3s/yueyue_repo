<?php
/**
 * 场次 订单列表
 *
 * @author willike
 * @since 2015/11/11
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 全部 all 待付款 unpaid 待确认 tbc 待签到 checkin 已完成  completed 已关闭 closed
$trade_type = $client_data['data']['param']['trade_type'];   // 交易类型
$activity_id = $client_data['data']['param']['activity_id'];   // 活动ID
$stage_id = $client_data['data']['param']['stage_id'];   // 场次ID
if (empty($user_id) || empty($activity_id) || empty($stage_id)) {
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
$trade_data = $mall_order_obj->get_order_list_by_activity_stage($activity_id, $stage_id, $status, false, '', 'lately_time DESC', $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$status' => $status,
        '$limit_str' => $limit_str,
        '$trade_data' => $trade_data,
    );  // for test
    return $cp->output($options);
}
$activity_info = array();
if (!empty($trade_data[0])) {
    $trade_activity = $trade_data[0];
    $activity_info = array(
        'type_name' => $trade_activity['type_name'],
        'activity_id' => $trade_activity['activity_id'],
        'title' => $trade_activity['activity_name'],
        'cover' => yueyue_resize_act_img_url($trade_activity['activity_images'], 260),
        'name' => $trade_activity['stage_title'],
        'time' => date('Y-m-d', $trade_activity['service_start_time']),
        'period' => date('Y-m-d H:i', $trade_activity['service_start_time']) . '至' . date('Y-m-d H:i', $trade_activity['service_end_time']),
        'sign' => $trade_activity['is_special'] == 1 ? '官方' : '',
    );
} else {
    $mall_api_obj = POCO::singleton('pai_mall_api_class');
    $activity_result = $mall_api_obj->get_goods_id_screenings_price_max_and_min($activity_id, $stage_id);
    if (empty($activity_result)) {  // 无数据
        $options['data'] = array(
            'result' => 0,
            'message' => '没有该活动场次信息',
            'activity' => array(),
            'list' => array(),
        );
        return $cp->output($options);
    }
    $activity_info = array(
        'title' => '[约活动]' . $activity_result['activity_name'],
        'name' => $activity_result['screenings_name'],
        'period' => date('Y-m-d H:i', $activity_result['time_s']) . '至' . date('Y-m-d H:i', $activity_result['time_e']),
        'prices' => '￥' . $activity_result['min_price'], //  . '-' . $activity_result['max_price'],
        'unit' => '/人 起',
        'attend_str' => '已报名人数 ',
        'attend_num' => intval($activity_result['has_join']),
        'total_num' => $activity_result['total_num'],
    );
}
$trade_list = array();
foreach ($trade_data as $value) {
    $status = $value['status'];  // 订单状态
    $status_str = $value['status_str'];  // 订单状态名称
    $is_seller_comment = $value['is_seller_comment'];  // 用户是否评价 , 1 已评价
    if ($status == 8 && $is_seller_comment == 0) {  // 已完成,未评价
        $status = 10;
        $status_str = '待评价';
    }
    $original_amount = $value['original_amount']; // 促销后价格 $value['original_subtotal']; // 最原始价格
    $show_price = '实付：￥' . sprintf('%.2f', $value['pending_amount']);  // 显示金额
    $trade_info = array(
        'order_sn' => $value['order_sn'],
        'goods_id' => $value['activity_id'],  // 活动ID
        'buyer_name' => $value['buyer_user_name'],  // 买家名称
        'prime_prices' => '￥' . $value['prime_prices'] . ' /人',  // 单价
        'standard' => $value['prices_spec'],  // 规格名称
        'status' => $status,
        'status_str' => $status_str,
        'price_str' => $show_price, // 用于显示的价格
        'cost' => $value['total_amount'], // 金额
        'original' => $original_amount, // 原价金额
        'attend' => '共 ' . $value['stock_num_total'] . '人',
        'link' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app', // 跳转订单详情
        'action' => interface_trade_seller_action($status, $is_seller_comment, $version),
    );
    $trade_list[] = $trade_info;
}
$num_result = $mall_order_obj->get_order_number_by_stage_for_seller($user_id, $activity_id, $stage_id);
if ($location_id == 'test2') {
    $options['data'] = $num_result;
    return $cp->output($options);
}
$options['data'] = array(
    'result' => 1,
    'summary' => $num_result,  // 汇总
    'activity' => $activity_info,
    'list' => $trade_list,
);
return $cp->output($options);




