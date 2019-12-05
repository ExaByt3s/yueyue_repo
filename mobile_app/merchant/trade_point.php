<?php

/**
 * ��������  ����
 *
 * @since 2015-7-3
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_seller($user_id);
if ($location_id == 'test') {
    $options['data'] = $num_result;
    return $cp->output($options);
}
// 0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
$wait_list = $tmp_list = array();
if (intval($num_result['wait_confirm']) > 0) {   // �ȴ�ȷ��
    $status = 1;
    $waitconfim_list = $mall_order_obj->get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_confirm'], '*');
    foreach ($waitconfim_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // �渶����ʾ
            continue;
        }
        $tmp_list[$buyer_user_id][] = array(
            'status' => $value['status'],
            'str' => 'ȷ�϶���',
//            'str' => $value['status_str'],
            'url' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app',
        );
    }
}
if (intval($num_result['wait_sign']) > 0) {   // �ȴ�ǩ��
    $status = 2;
    $waitsign_list = $mall_order_obj->get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_sign'], '*');
    foreach ($waitsign_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // �渶����ʾ
            continue;
        }
        $tmp_list[$buyer_user_id][] = array(
            'status' => $value['status'],
            'str' => 'ɨ��ǩ��',
//            'str' => $value['status_str'],
            'url' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app',
        );
    }
}
if (intval($num_result['wait_comment']) > 0) {   // �ȴ�����
    $status = 8;
    $waitcomm_list = $mall_order_obj->get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_comment'], '*', 0);
    foreach ($waitcomm_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
        $order_type = $value['order_type'];
        if ($order_type == 'payment') {  // �渶����ʾ
            continue;
        }
        $tmp_list[$buyer_user_id][] = array(
            'status' => 10,
            'str' => 'ȥ����',
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
