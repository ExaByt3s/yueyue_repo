<?php

/**
 * ��������  ����
 * 
 * @since 2015-7-3
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input(array('be_check_token' => false));
$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$api_obj = POCO::singleton('pai_mall_api_class');
$num_result = $api_obj->api_get_order_number_for_seller($user_id);
if ($location_id == 'test') {
    $options['data'] = $num_result;
    $cp->output($options);
    exit;
}
// 0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
$wait_list = array();
$tmp_list = array();
//if (intval($num_result['wait_pay']) > 0) {   // �ȴ�֧�� ( �̼Ұ� ������ʾ 2015-7-9 )
//    $status = 0;
//    $waitpay_list = $mall_order_obj->get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_pay'], '*');
//    foreach ($waitpay_list as $value) {
//        $buyer_user_id = $value['buyer_user_id'];
//        $tmp_list[$buyer_user_id][] = array(
//            'status' => $value['status'],
//            'str' => $value['status_str'],
//            'url' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app',
//        );
//    }
//}
if (intval($num_result['wait_confirm']) > 0) {   // �ȴ�ȷ��
    $status = 1;
    $waitconfim_list = $api_obj->api_get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_confirm'], '*');
    foreach ($waitconfim_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
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
    $waitsign_list = $api_obj->api_get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_sign'], '*');
    foreach ($waitsign_list as $value) {
        $buyer_user_id = $value['buyer_user_id'];
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
    // ������ �Ƚ�����, ���� limit Ϊ �������� * 2
    $waitcomm_list = $api_obj->api_get_order_list_for_seller($user_id, 0, $status, false, 'order_id DESC', '0,' . ($num_result['wait_comment'] * 2), '*');
    foreach ($waitcomm_list as $value) {
        if ($value['is_seller_comment'] == '1') {  // �û����۹��� 
            continue;
        }
        $buyer_user_id = $value['buyer_user_id'];
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
    );
    $cp->output($options);
    exit;
}
if ($location_id == 'test2') { //
    $options['data'] = $tmp_list;
    $cp->output($options);
    exit;
}
foreach ($tmp_list as $key => $value) {
    $wait_list[] = array(
        'user_id' => strval($key),
        'msg_list' => $value,
    );
}

$options['data']['list'] = $wait_list;
$cp->output($options);
