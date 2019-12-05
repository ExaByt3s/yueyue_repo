<?php

/**
 * �ҵ���ҳ
 *
 * @author heyaohua
 * @editor chenweibiao<chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
if (version_compare($version, '3.3', '>')) {
    $data = array(
        'result' => 0,
        'message' => '�ýӿ���ͣ��, ��ʹ�� sell_index.php �ӿ�.',
    );
    return $cp->output(array('data' => $data));
}
$user_id = $client_data['data']['param']['user_id'];
$obj = POCO::singleton('pai_user_class');
$user_info = $obj->get_user_info($user_id);  // ��ȡ�û� ��Ϣ
if (empty($user_info)) {
    return $cp->output(array('data' => array()));
}
$user_level_obj = POCO::singleton('pai_user_level_class');
$user_level = $user_level_obj->get_user_level($user_id);

//$credit_url = 'http://yp.yueus.com/mall/user/cetificate/list.php';   // ���õȼ�
$credit_url = 'http://yp.yueus.com/mall/user/topic/index.php?topic_id=749&online=1';
$setup_url = 'http://yp.yueus.com/mobile/app?from_app=1#account/setup';
$bill_url = 'http://yp.yueus.com/mobile/m2/mine/bill/?type=trade';
$pw_url = 'http://yp.yueus.com/mobile/app?from_app=1#account/setup/bind/enter_pw/form_setup';
$event_url = 'http://yp.yueus.com/mall/user/act/order_list.php?type=unpaid';   // ���Ķ����б�
$alipay_url = 'http://yp.yueus.com/mobile/m2/mine/bind_alipay/';
$coupon_url = 'http://yp.yueus.com/mall/user/coupon/list.php';   // �Ż�ȯ
$recharge_url = 'http://yp.yueus.com/mobile/m2/recharge/index.php';
$trade_url = 'http://yp.yueus.com/mall/user/order/list.php?type_id=0&status=0';  // �����б�
//if (version_compare($version, '3.2', '>')) {
//    $trade_url = 'http://yp.yueus.com/mall/user/test/order/list.php?type_id=0&status=0';  // �����б�
//}
$index_info = array(
    'mid' => '122LB08002',
    'user_id' => $user_id,
    'nickname' => get_user_nickname_by_user_id($user_id),
    'icon' => get_user_icon($user_id, 165, TRUE),
    'location_id' => $user_info['location_id'],
    'city_name' => get_poco_location_name_by_location_id($user_info ['location_id']), // ���ڳ���
    'user_level' => $user_level, // �û���֤�ȼ�
    // һЩURL
    'home_url' => 'http://yp.yueus.com/mobile/app?from_app=1#zone/' . $user_id . '/cameraman',
    'home_url_wifi' => 'http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/' . $user_id . '/cameraman',
    'code_url' => 'yueyue://goto?type=inner_app&pid=1220092', // ��ά��ǩ��ȯ
    'favor_url' => 'yueyue://goto?type=inner_app&pid=1220148&user_id=' . $user_id, // �ղ� 1220148�ղ�-�̼��б�,1220149�ղ�-��Ʒ�б�,1220150����ҳ-�̼��ղ�
    'recharge_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($recharge_url) . '&wifi_url=' . urlencode($recharge_url) . '&showtitle=1',
    'credit_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($credit_url) . '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $credit_url)) . '&showtitle=2',
    'yuepai_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($trade_url) . '&wifi_url=' . urlencode($trade_url) . '&showtitle=1', // ��������
//    'yuepai_url' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220120&type=inner_app',  // ��������
    'setup_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($setup_url) . '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $setup_url)),
    'bill_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($bill_url) . '&wifi_url=' . urlencode($bill_url) .
        '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $bill_url)) . '&showtitle=1', // �˵��б�
    'pw_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($pw_url) . '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $pw_url)),
    'alipay_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($alipay_url) . '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $alipay_url)) . '&showtitle=2',
    'event_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($event_url) . '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $event_url)) . '&showtitle=1',
    'coupon_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($coupon_url) . '&wifi_url=' . urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $coupon_url)) . '&showtitle=1',
    // һЩ ͳ��
    'waipai_num' => strval(count_waipai_order_num($user_id)), // ���Ķ�����
    'yuepai_num' => '0', // Լ�Ķ�����(���񶩵�)
    'code_num' => '0', // ǩ��ȯ����
    // ����
    'share' => array(),
    'edit' => array(
        'title' => '��������',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&buyer_id=' . $user_id . '&pid=1220123&type=inner_app',
    ),
);

// Լ�Ķ�����
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_buyer($user_id);
if ($num_result) {
    $index_info['yuepai_num'] = strval(intval($num_result['wait_pay']) + intval($num_result['wait_sign']));
//    $index_info['yuepai_num'] = strval(intval($num_result['all']));
}

// ǩ��ȯ����
$activity_obj = POCO::singleton('pai_activity_code_class');
$ticket_arr = $activity_obj->get_new_act_ticket($user_id, '0,1000');
$ticket_num = count($ticket_arr);
$index_info['code_num'] = strval($ticket_num);

// �Ż�ȯ����
$coupon_obj = POCO::singleton('pai_coupon_class');
$coupon_num = $coupon_obj->get_user_coupon_list_by_tab('available', $user_id, true);
$index_info['coupon_num'] = strval($coupon_num);

// �û�����
$cameraman_card_obj = POCO::singleton('pai_cameraman_card_class');
$share_text = $cameraman_card_obj->get_share_text($user_id);
$index_info['share'] = $share_text;

// TT ����
$index_info['tt_url'] = 'yueyue://goto?type=inner_app&pid=1220080';

$options['data'] = $index_info;
return $cp->output($options);
