<?php

/**
 * 交易提醒  数量
 *
 * @since 2015-7-9
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_buyer($user_id);
if ($location_id == 'test') {
    $options['data'] = $num_result;
    return $cp->output($options);
}
// 0待支付，1待确认，2待签到，7已关闭，8已完成
$detail_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=';  // 订单详情URL
$tmp_list = array();
if (intval($num_result['wait_pay']) > 0) {   // 等待支付
    $status = 0;
    $waitpay_list = $mall_order_obj->get_order_list_for_buyer($user_id, 0, $status, false, '', '0,' . $num_result['wait_pay'], '*');
    foreach ($waitpay_list as $value) {
        $seller_user_id = $value['seller_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // 面付不显示
            continue;
        }
        $link_url = $detail_url . $value['order_sn'];
        $wifi_link_url = str_replace('//yp.', '//yp-wifi.', $detail_url) . $value['order_sn'];
        $tmp_list[$seller_user_id][] = array(
            'status' => $value['status'],
            'str' => '去支付',
//            'str' => $value['status_str'],
//            'url' => 'yueyue://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1220021&type=inner_app',
            'url' => 'yueyue://goto?type=inner_web&showtitle=1&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url) . '&showtitle=1',
        );
    }
}
if (intval($num_result['wait_sign']) > 0) {   // 等待签到
    $status = 2;
    $waitsign_list = $mall_order_obj->get_order_list_for_buyer($user_id, 0, $status, false, '', '0,' . $num_result['wait_sign'], '*');
    foreach ($waitsign_list as $value) {
        $seller_user_id = $value['seller_user_id'];
        $link_url = $detail_url . $value['order_sn'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // 面付不显示
            continue;
        }
        $wifi_link_url = str_replace('//yp.', '//yp-wifi.', $detail_url) . $value['order_sn'];
        $tmp_list[$seller_user_id][] = array(
            'status' => $value['status'],
            'str' => '出示二维码',
//            'str' => $value['status_str'],
//            'url' => 'yueyue://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1220021&type=inner_app',
            'url' => 'yueyue://goto?type=inner_web&showtitle=1&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url) . '&showtitle=1',
        );
    }
}
if (intval($num_result['wait_comment']) > 0) {   // 等待评价
    $status = 8;
    $waitcomm_list = $mall_order_obj->get_order_list_for_buyer($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_comment'], '*', 0);
    foreach ($waitcomm_list as $value) {
        $seller_user_id = $value['seller_user_id'];
        $order_type = $value['order_type'];
        $link_url = $detail_url . $value['order_sn'];
        if ($order_type == 'payment') {  // 面付不提示
            continue;
//            $link_url = 'http://yp.yueus.com/mall/wallet-test/yue_pay/detail.php?order_sn=' . $value['order_sn'];
        }
        $wifi_link_url = str_replace('//yp.', '//yp-wifi.', $link_url);
        $tmp_list[$seller_user_id][] = array(
            'status' => '10',
            'order_type' => $order_type,
            'str' => '去评价',
//            'url' => 'yueyue://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1220021&type=inner_app',
            'url' => 'yueyue://goto?type=inner_web&showtitle=1&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url) . '&showtitle=1',
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

$wait_list = array();
foreach ($tmp_list as $key => $value) {
    $wait_list[] = array(
        'user_id' => strval($key),
        'msg_list' => $value,
    );
}

$options['data']['list'] = $wait_list;
return $cp->output($options);
