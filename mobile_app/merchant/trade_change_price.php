<?php

/**
 * ���� �ļ�
 * 
 * @author heyaohua
 * @since 2015-8-20
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];         // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];             // ��ǰ�û�ID
$order_sn = $client_data['data']['param']['order_sn'];            // ����ID
$change_price = $client_data['data']['param']['change_price'];        // �ļ۽��
$change_price_reason = $client_data['data']['param']['change_price_reason']; // �ļ�����

if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '���׺�Ϊ��');
    $cp->output($options);
    exit;
}

if (empty($change_price)) {
    $options['data'] = array('result' => -1, 'message' => '�ļ۽��Ϊ��');
    $cp->output($options);
    exit;
}

$pai_mall_order_obj = POCO::singleton('pai_mall_order_class');
$result = $pai_mall_order_obj->change_order_price($order_sn, $user_id, $change_price, $change_price_reason);

$options['data'] = $result;
$cp->output($options);
