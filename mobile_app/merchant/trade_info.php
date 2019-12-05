<?php

/**
 * ���� ����
 *
 * @since 2015-6-17
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID

if (empty($user_id) || empty($order_sn)) {
    return $cp->output(array('data' => array()));
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
$order_info = $mall_order_obj->get_order_full_info($order_sn);
if (empty($order_info)) {
    return $cp->output(array('data' => array()));
}
if ($location_id == 'test') {
    $options['data'] = $order_info;
    return $cp->output($options);
}
$type_id = $order_info['type_id'];  // �����ID
$status = $order_info['status'];
$is_seller_comment = $order_info['is_seller_comment'];  // �Ƿ�����
$status_str = $order_info['status_str'];
if ($status == 8 && $is_seller_comment == 0) {  // δ����
    $status = 10;
    $status_str = '������';
}
$goods_list = $type_id == 42 ? $order_info['activity_list'] : $order_info['detail_list'];  // ��Ʒ����
$goods_info = $goods_list[0];  // ��Ʒ��Ϣ, ȡ��һ��
// ��ƷID/�ID
$goods_id = $type_id == 42 ? $goods_info['activity_id'] : $goods_info['goods_id'];
// ��װ������������
$property_data = interface_trade_property_data($goods_info, $version);
$buyer_user_id = $order_info['buyer_user_id'];  // ���ID
$buyer_user_name = empty($order_info['buyer_name']) ? get_user_nickname_by_user_id($buyer_user_id) : $order_info['buyer_name'];    // �������
$original_amount_str = $order_info['original_amount_str'];  // ԭ��/������/�ļ�֮��� ����ʾ
$original_amount_str = (empty($original_amount_str) || $original_amount_str == '()') ? '' : $original_amount_str;
$trade_data = array(
    'title' => $type_id == 42 ? '���������' : '���񶩵�����',
    'order_sn' => $order_info['order_sn'], // �������
    'status' => $status, // ״̬
    'status_str' => $status_str, // ״̬
    'notice' => '��ע��' . (empty($order_info['description']) ? '--' : $order_info['description']), // ��ע
    'remark' => array('title' => '��ע��', 'value' => $order_info['description']), // ��ע for 1.3.0
//    'expire_time' => $order_info['expire_time'], // ��������ʱ��(���м�����Чʱ��)
    'expire' => $order_info['expire_str'], // ��������ʱ��
    // ���
    'customer' => array(
        'title' => '��ϵ���',
        'user_id' => $buyer_user_id,
        'name' => '��ң�',
        'value' => $buyer_user_name,
        'avatar' => $order_info['buyer_icon'],  // ������ͷ��
        'request' => 'yueseller://goto?user_id=' . $buyer_user_id . '&buyer_id=' . $buyer_user_id . '&pid=1250027&type=inner_app', // ���ҳ��
        'consult' => 'yueseller://goto?user_id=' . $user_id . '&receiver_id=' . $buyer_user_id .
            '&receiver_name=' . urlencode(mb_convert_encoding($buyer_user_name, 'utf-8', 'gbk')) . '&pid=1250025&type=inner_app', // �������
    ),
    // ���
    'cost' => array(
        'name' => '�ܼۣ�',
        'value' => '��' . sprintf('%.2f', $order_info['total_amount']),
        'total' => sprintf('%.2f', $order_info['total_amount']),  // �ܽ������ļۺ�Ľ�
        'promotion' => 0.00,  // �����۸�
        'original' => sprintf('%.2f', $order_info['original_amount']), // ������Ķ����������ļ�ǰ�Ľ� $order_info['original_subtotal']), // ��ԭʼ�۸�
        'original_str' => $original_amount_str,
//        'initial' => sprintf('%.2f', $order_info['original_subtotal']),  // ����۸�
    ),
    'bill' => array(
        array('title' => '������', 'value' => '��' . $order_info['total_amount']),
        array('title' => '�Ż�ȯ', 'value' => '-��' . $order_info['discount_amount']),
        array('title' => 'ʵ����', 'value' => '��' . $order_info['pending_amount']),
    ),
    'showing' => $property_data['showing'],  // �
    'goods' => $property_data['goods'],  // ��Ʒ��Ϣ
    'detail' => $property_data['detail'],  // ��������
    'property' => $property_data['property'],  // ��������
    'trade' => array(
        array('title' => '������ţ�', 'value' => $order_info['order_sn']),
        array('title' => '�µ�ʱ�䣺', 'value' => date('Y-m-d H:i', $order_info['add_time'])),
        array('title' => '֧��ʱ�䣺', 'value' => $order_info['is_pay'] == '1' ? date('Y-m-d H:i', $order_info['pay_time']) : '--'),
        array('title' => '�ͷ��绰��', 'value' => '4000-82-9003'),
    ),
    'action' => btn_action($order_info['status'], $is_seller_comment, $version),
);
// �鿴��Ʒ �Ƿ���Ҫ �����ת
if ($order_info['is_special'] != 1) {
    // ��������Ʒ, �������� (����ת )
    if ($type_id == 42) {  // �����
        // �����
        $trade_data['goods']['request'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
    }else{
        $trade_data['goods']['request'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250007&type=inner_app';
    }
}
// �Ƿ����
if ($goods_info['is_goods_promotion'] == 1 || $goods_info['is_activity_promotion'] == 1) {
    // �μӴ���
    $total_amount = sprintf('%.2f', $order_info['total_amount']);
    if (version_compare($version, '1.3', '<') && !empty($original_amount_str)) {
        $trade_data['cost']['value'] = '��' . $total_amount . '(' . $original_amount_str . ')';
    }
    $trade_data['cost']['promotion'] = $goods_info['goods_promotion_amount']; // �����۸�
    // ������Ϣ
    $promotion_info = $type_id == 42 ? $order_info['activity_promotion_info'] : $order_info['goods_promotion_info'];
    $period = str_replace('-', '.', $promotion_info['start_time']) . '-' . str_replace('-', '.', $promotion_info['end_time']);
    $trade_data['promotion'] = array(
        'abate' => '��߿�ʡ��' . $promotion_info['cal_used_amount'],
        'notice' => $promotion_info['type_name'],
        'period' => $period,
        'description' => $period . "\n" . $promotion_info['promotion_desc'],
        'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
    );
}
// �Ƿ�ļ�
if ($order_info['is_change_price'] == 1) {
    // original_subtotal��ԭʼ�۸�pending_amountʵ��֧���۸�discount_amount�Ż�ȯ�۸�total_amount�ܽ������ļۺ�Ľ�,original_amount������Ķ����������ļ�ǰ�Ľ�
    $trade_data['promo'] = '�ļ�ԭ��' . $order_info['change_price_reason'];
    $original_amount = $order_info['original_subtotal']; // $order_info['original_amount'];
    $original_amount = ($original_amount == intval($original_amount)) ? intval($original_amount) : sprintf('%.2f', $original_amount);
    $total_amount = sprintf('%.2f', $order_info['total_amount']);
//    $total_amount = ($total_amount == intval($total_amount)) ? intval($total_amount) : sprintf('%.2f', $total_amount);
    if (version_compare($version, '1.3', '<') && !empty($original_amount_str)) {
        $trade_data['cost']['value'] = '��' . $total_amount . '(' . $original_amount_str . ')';
    }
    $trade_data['cost']['total'] = strval($total_amount);
}
// ��������
$trade_process = array();
foreach ($order_info['process_list'] as $process) {
    $trade_process[] = array(
        'notice' => $process['process_content'],
        'name' => $process['process_nickname'],
        'avatar' => $process['process_icon'],
        'time' => date('Y-m-d H:i', $process['process_time']),
        'reason' => '',  // Ԥ���ֶ�,�������
    );
}
$trade_data['process'] = $trade_process;

/**
 * ��ť ����
 *
 * @param string $status ����״̬
 * @param string $is_seller_comment �̼�����
 * @param string $version
 * @return array
 */
function btn_action($status, $is_seller_comment, $version) {
    if ($is_seller_comment == 1) {  // �̼�������
        return array();
    }
    // ��ť�İ�
    $action_arr = array(
        0 => '�رն���.close',
        1 => '�ܾ�����.refuse|���ܶ���.accept',
        2 => 'ɨ��ǩ��.sign',
//        2 => 'ȡ�����ײ��˿�.close|ɨ��ǩ��.sign',
//        7 => 'ɾ������.delete',
        8 => '���۶Է�.appraise'
    );
    if (version_compare($version, '1.1') >= 0) {
        $action_arr[0] = '�رն���.close|�޸ļ۸�.pending';
    }
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

$options['data'] = $trade_data;
return $cp->output($options);
