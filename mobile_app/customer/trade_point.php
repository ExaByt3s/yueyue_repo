<?php

/**
 * ��������  ����
 *
 * @since 2015-7-9
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_buyer($user_id);
if ($location_id == 'test') {
    $options['data'] = $num_result;
    return $cp->output($options);
}
// 0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
$detail_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=';  // ��������URL
$tmp_list = array();
if (intval($num_result['wait_pay']) > 0) {   // �ȴ�֧��
    $status = 0;
    $waitpay_list = $mall_order_obj->get_order_list_for_buyer($user_id, 0, $status, false, '', '0,' . $num_result['wait_pay'], '*');
    foreach ($waitpay_list as $value) {
        $seller_user_id = $value['seller_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // �渶����ʾ
            continue;
        }
        $link_url = $detail_url . $value['order_sn'];
        $wifi_link_url = str_replace('//yp.', '//yp-wifi.', $detail_url) . $value['order_sn'];
        $tmp_list[$seller_user_id][] = array(
            'status' => $value['status'],
            'str' => 'ȥ֧��',
//            'str' => $value['status_str'],
//            'url' => 'yueyue://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1220021&type=inner_app',
            'url' => 'yueyue://goto?type=inner_web&showtitle=1&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url) . '&showtitle=1',
        );
    }
}
if (intval($num_result['wait_sign']) > 0) {   // �ȴ�ǩ��
    $status = 2;
    $waitsign_list = $mall_order_obj->get_order_list_for_buyer($user_id, 0, $status, false, '', '0,' . $num_result['wait_sign'], '*');
    foreach ($waitsign_list as $value) {
        $seller_user_id = $value['seller_user_id'];
        $link_url = $detail_url . $value['order_sn'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // �渶����ʾ
            continue;
        }
        $wifi_link_url = str_replace('//yp.', '//yp-wifi.', $detail_url) . $value['order_sn'];
        $tmp_list[$seller_user_id][] = array(
            'status' => $value['status'],
            'str' => '��ʾ��ά��',
//            'str' => $value['status_str'],
//            'url' => 'yueyue://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1220021&type=inner_app',
            'url' => 'yueyue://goto?type=inner_web&showtitle=1&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url) . '&showtitle=1',
        );
    }
}
if (intval($num_result['wait_comment']) > 0) {   // �ȴ�����
    $status = 8;
    $waitcomm_list = $mall_order_obj->get_order_list_for_buyer($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_comment'], '*', 0);
    foreach ($waitcomm_list as $value) {
        $seller_user_id = $value['seller_user_id'];
        $order_type = $value['order_type'];
        $link_url = $detail_url . $value['order_sn'];
        if ($order_type == 'payment') {  // �渶����ʾ
            continue;
//            $link_url = 'http://yp.yueus.com/mall/wallet-test/yue_pay/detail.php?order_sn=' . $value['order_sn'];
        }
        $wifi_link_url = str_replace('//yp.', '//yp-wifi.', $link_url);
        $tmp_list[$seller_user_id][] = array(
            'status' => '10',
            'order_type' => $order_type,
            'str' => 'ȥ����',
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
