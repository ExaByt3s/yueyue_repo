<?php

/**
 * ���� ����
 *
 * @since 2015-6-29
 * @author chenweibiao <chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID

if (empty($user_id) || empty($order_sn)) {
    return $cp->output(array('data' => ''));
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
$order_info = $mall_order_obj->get_order_full_info($order_sn);
if (empty($order_info)) {
    return $cp->output(array('data' => ''));
}
if ($location_id == 'test') {
    $options['data'] = $order_info;
    return $cp->output($options);
}
$goods = $order_info['detail_list'][0];
// ��װ������������
$trade_data = array(
    'order_sn' => $order_info['order_sn'], // �������
    'status' => $order_info['status'], // ״̬
    'status_str' => $order_info['status_str'], // ״̬
    // ���
    'cost' => array(
        'name' => '������',
        'value' => '��' . $order_info['total_amount'],
    ),
    'remark' => array(
        'name' => '��ע��',
        'value' => $order_info['description'],
    ),
    'goods' => array(
        'id' => $goods['goods_id'],
        'name' => $goods['goods_name'],
        'image' => $goods['goods_images'],
        'price' => '��' . $goods['prices'], // amount
        'property' => array('name' => '���', 'value' => $goods['prices_spec'])
    ),
    // �̼�
    'seller' => array(
        'name' => '�̼ң�',
        'value' => get_seller_nickname_by_user_id($order_info['seller_user_id']),
        'request' => 'yueseller://goto?user_id=' . $user_id . '&seller_user_id=' . $order_info['seller_user_id'] . '&pid=1220103&type=inner_app',
    ),
    'trade' => array(
        array('title' => '�����ţ�', 'value' => $order_info['order_sn']),
//        array('title' => '�µ�ʱ�䣺', 'value' => date('Y-m-d H:i', $order_info['add_time'])),
        array('title' => '�ɽ�ʱ�䣺', 'value' => $order_info['is_pay'] == '1' ? date('Y-m-d H:i', $order_info['pay_time']) : date('Y-m-d H:i', $order_info['add_time'])),
    ),
    'action' => interface_trade_buyer_action($order_info['status'], $order_info['is_buyer_comment']),
);
// �鿴 �Ƿ� ��Ʒ �Ƿ���Ҫ �����ת
if ($order_info['is_special'] == 1) {
    $trade_data['goods']['request'] = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods['goods_id'] . '&pid=1220111&type=inner_app';
}
$options['data'] = $trade_data;
return $cp->output($options);