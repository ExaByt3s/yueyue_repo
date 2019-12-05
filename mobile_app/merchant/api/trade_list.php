<?php

/**
 * ���� �б�
 *
 * @since 2015-6-17
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// ȫ�� all ������ unpaid ��ȷ�� tbc ��ǩ�� checkin �����  completed �ѹر� closed
$trade_type = $client_data['data']['param']['trade_type'];   // ��������
$type_id = intval($client_data['data']['param']['type_id']);   // ��Ʒ����
$version = $client_data['data']['version'];  // �汾��
if (empty($user_id)) {
    $options['data']['list'] = array();
    return $cp->output($options);
}
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
    $rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}
// ������ 0, ��ȷ�� 1, ��ǩ�� 2, �ѹر� 7, ����� 8, ���� -1 
switch ($trade_type) {
    case 'unpaid':  // ������
        $status = 0;
        break;
    case 'tbc':  // ��ȷ��
        $status = 1;
        break;
    case 'checkin':  // ��ǩ��
        $status = 2;
        break;
    case 'completed':  // �����
        $status = 8;
        break;
    case 'closed':  // �ѹر�
        $status = 7;
        break;
    default:  // ����
        $status = -1;
        break;
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
$trade_data = $mall_order_obj->get_order_list_by_detail_for_seller($user_id, $type_id, $status, false, 'lately_time DESC', $limit_str, '*');
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$type_id' => $type_id,
        '$status' => $status,
        '$limit_str' => $limit_str,
        '$trade_data' => $trade_data,
    );  // for test
    return $cp->output($options);
}
$trade_list = array();
foreach ($trade_data as $value) {
    $order_sn = $value['order_sn'];
    $status = $value['status'];
    $is_seller_comment = $value['is_seller_comment'];  // �Ƿ�����
    $status_str = $value['status_str'];
    if ($status == 8 && $is_seller_comment == 0) {  // δ����
        $status = 10;
        $status_str = '������';
    }
    $show_price = '��' . $value['total_amount'];  // ��ʾ���
    $original_amount = $value['original_amount']; // ������۸� $value['original_subtotal']; // ��ԭʼ�۸�
    if ($value['is_change_price'] == 1 || $value['detail_list'][0]['is_goods_promotion'] == 1) {
        // �ļ� or ����
        $show_price = '��' . $value['total_amount'] . '(' . $value['original_amount_str'] . ')';
    }
    $buyer_user_id = $value['buyer_user_id'];
    $trade_info = array(
        'order_sn' => $order_sn,
        'type_id' => $value['type_id'], // ��ƷƷ��
        'title' => get_user_nickname_by_user_id($buyer_user_id), // ����, ������
        'desc' => $value['detail_list'][0]['goods_name'], // ����, ��������Ʒ��
        'link' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $order_sn . '&pid=1250022&type=inner_app', // ��ת��������
        'status' => $status, // ״̬
        'status_str' => $status_str, // ״̬����
        'price_str' => $show_price, // ������ʾ�ļ۸�
        'cost' => $value['total_amount'], // ���
        'original' => $original_amount, // ԭ�۽��
        'goods_id' => $value['detail_list'][0]['goods_id'], // ��ƷID
        'thumb' => $value['detail_list'][0]['goods_images'], // Ԥ��ͼ
        'action' => interface_trade_seller_action($value['status'], $value['is_seller_comment'], $version),
    );
    $trade_list[] = $trade_info;
}

$options['data']['list'] = $trade_list;
return $cp->output($options);