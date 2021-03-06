<?php

/**
 * 交易提醒  数量
 *
 * @since 2015-7-3
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_seller($user_id);
if ($location_id == 'test') {
    $options['data'] = $num_result;
    return $cp->output($options);
}
// 0待支付，1待确认，2待签到，7已关闭，8已完成
$wait_list = $tmp_list = array();
if (intval($num_result['wait_confirm']) > 0) {   // 等待确认
    $status = 1;
    $waitconfim_list = $mall_order_obj->get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_confirm'], '*');
    foreach ($waitconfim_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // 面付不显示
            continue;
        }
        $tmp_list[$buyer_user_id][] = array(
            'status' => $value['status'],
            'str' => '确认订单',
//            'str' => $value['status_str'],
            'url' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app',
        );
    }
}
if (intval($num_result['wait_sign']) > 0) {   // 等待签到
    $status = 2;
    $waitsign_list = $mall_order_obj->get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_sign'], '*');
    foreach ($waitsign_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // 面付不显示
            continue;
        }
        $tmp_list[$buyer_user_id][] = array(
            'status' => $value['status'],
            'str' => '扫码签到',
//            'str' => $value['status_str'],
            'url' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app',
        );
    }
}
if (intval($num_result['wait_comment']) > 0) {   // 等待评价
    $status = 8;
    $waitcomm_list = $mall_order_obj->get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_comment'], '*', 0);
    foreach ($waitcomm_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // 面付不显示
            continue;
        }
        $tmp_list[$buyer_user_id][] = array(
            'status' => 10,
            'str' => '去评价',
            'url' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app',
        );
    }
}
if ($location_id == 'test1') { //
    $options['data'] = array(
        'waitpay_list' => $waitpay_list,
        'waitconfim_list' => $waitconfim_list,
        'waitsign_list' => $waitsign_list,
        'waitcomm_list' => $waitcomm_list,
        '$tmp_list' => $tmp_list,
    );
    return $cp->output($options);
}
foreach ($tmp_list as $key => $value) {
    $wait_list[] = array(
        'user_id' => strval($key),
        'msg_list' => $value,
    );
}

$options['data']['list'] = $wait_list;
return $cp->output($options);
