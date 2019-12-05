<?php

/**
 * ���� ����
 * 
 * @since 2015-6-29
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID

if (empty($user_id) || empty($order_sn)) {
    $cp->output(array('data' => ''));
    exit;
}
$api_obj = POCO::singleton('pai_mall_api_class');   // ʵ�����̼ҽ��׶���
$order_info = $api_obj->api_get_order_full_info($order_sn);
if (empty($order_info)) {
    $cp->output(array('data' => ''));
    exit;
}
if ($location_id == 'test') {
    $options['data'] = $order_info;
    $cp->output($options);
    exit;
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
        'value' => get_user_nickname_by_user_id($order_info['seller_user_id']),
        'request' => 'yueseller://goto?user_id=' . $user_id . '&seller_user_id=' . $order_info['seller_user_id'] . '&pid=1220103&type=inner_app',
    ),
    'trade' => array(
        array('title' => '�����ţ�', 'value' => $order_info['order_sn']),
//        array('title' => '�µ�ʱ�䣺', 'value' => date('Y-m-d H:i', $order_info['add_time'])),
        array('title' => '�ɽ�ʱ�䣺', 'value' => $order_info['is_pay'] == '1' ? date('Y-m-d H:i', $order_info['pay_time']) : date('Y-m-d H:i', $order_info['add_time'])),
    ),
    'action' => btn_action($order_info['status'], $order_info['is_buyer_comment']),
);

$options['data'] = $trade_data;
$cp->output($options);

/**
 * ��ť ����
 * 
 * @param string $status ����״̬
 * @param string $is_buyer_comment ����Ƿ�����
 * @return array 
 */
function btn_action($status, $is_buyer_comment) {
    if ($is_buyer_comment == 1) {  // ���������
        return array();
    }
    // ��ť�İ�
    $action_arr = array(
        0 => 'ȡ������.close',
//        1 => '�ܾ�.refuse|ͬ��.accept',
        2 => '�����˿�.close|��ʾ��ά��.sign',
        7 => 'ɾ������.delete',
        8 => '���۶Է�.appraise'
    );
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}
