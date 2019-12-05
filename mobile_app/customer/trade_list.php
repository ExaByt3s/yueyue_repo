<?php

/**
 * ���׼�¼ �б�
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-7-1
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
//require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// ȫ�� all ������ unpaid ��ȷ�� tbc ��ǩ�� checkin �����  completed �ѹر� closed
$trade_type = $client_data['data']['param']['trade_type'];   // ��������
$type_id = $client_data['data']['param']['type_id'];   // ��Ʒ����
$type_id = intval($type_id); // ĳ��Ʒ��/0ȫ��
if (empty($user_id)) {
    $options['data'] = array(
        'result' => 0,
        'message' => '�û�IDΪ��',
        'list' => array(),
    );
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
$trade_data = $mall_order_obj->get_order_list_for_buyer($user_id, $type_id, $status, false, 'order_id DESC', $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$type_id' => $type_id,
        '$status' => $status,
        '$trade_data' => $trade_data,
    );
    return $cp->output($options);
}
$trade_total = 0;
if (!empty($trade_data)) {
    // ����
    $trade_total = $mall_order_obj->get_order_list_for_buyer($user_id, $type_id, $status, true);
}
$trade_list = array();
foreach ($trade_data as $value) {
    $order_sn = $value['order_sn'];
    $status = $value['status'];
    $type_id = $value['type_id'];  // ����ID
    $is_buyer_comment = $value['is_buyer_comment'];  // �Ƿ�����
    $status_str = $value['status_str'];
    $appraise_link = '';
    if ($status == 8 && $is_buyer_comment == 0) {  // δ����
        $status = 10;
        $status_str = 'δ����';
        $appraise_url = 'http://yp.yueus.com/mall/user/comment/?order_sn=' . $order_sn . '&stage_id=';
        $appraise_link = 'yueyue://goto?type=inner_web&url=' . urlencode($appraise_url) . '&wifi_url=' . urlencode($appraise_url) . '&showtitle=1';
    }
    // ����
    $detail_url = 'http://yp.yueus.com/mall/user/test/order/detail.php?order_sn=' . $order_sn;
    $detail_link = 'yueyue://goto?type=inner_web&url=' . urlencode($detail_url) . '&wifi_url=' . urlencode($detail_url) . '&showtitle=1';

    $seller_user_id = $value['seller_user_id'];
    $goods_id = $value['detail_list'][0]['goods_id']; // ��ƷID

    $title = $value['detail_list'][0]['goods_name'];// ��Ʒ����
    $service_time = $value['detail_list'][0]['service_time'];  // ����ʱ��
    $cover = $value['detail_list'][0]['goods_images']; // ����
    if ($type_id == 42) {  // �
        $goods_id = $value['activity_list'][0]['activity_id']; // �ID
        $title = $value['activity_list'][0]['activity_name']; // ���
        $service_time = $value['activity_list'][0]['service_start_time'];  // ����ʱ��
        $cover = $value['activity_list'][0]['activity_images'];
    } elseif ($type_id == 20) { // �渶
        $goods_id = '';
        $title = 'ֱ��֧��';  // ����
        $service_time = $value['pay_time'];  // ֧��ʱ��
        $cover = $value['seller_icon'];
    }
    $service_time = (empty($service_time) || $service_time == '--') ? '--' : date('Y.m.d', $service_time);
    $trade_info = array(
        'order_sn' => $order_sn,
        'type_id' => $value['type_id'], // Ʒ��
        'seller' => array(
            'user_id' => $value['seller_user_id'],
            'name' => $value['seller_name'],
            'avatar' => $value['seller_icon'],
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $value['seller_user_id'] . '&pid=1220103&type=inner_app',
        ),
        'title' => $title, // ��������
        'goods_id' => $goods_id, // ��ƷID
        'cover' => $type_id == 20 ? $cover : yueyue_resize_act_img_url($cover, 260), // Ԥ��ͼ
        'status' => $status, // ״̬
        'status_str' => $status_str, // ״̬����
        'cost' => '��' . $value['total_amount'], // ���
        'order_time' => '�µ�ʱ�䣺' . date('Y.m.d', $value['add_time']),
        'service_time' => '����ʱ�䣺' . $service_time,
        'link' => $detail_link, // ��ת ��������
        'appraise_link' => $appraise_link,  // ����
    );
    $trade_list[] = $trade_info;
}

$options['data'] = array(
    'total' => $trade_total,
    'list' => $trade_list,
);
return $cp->output($options);
