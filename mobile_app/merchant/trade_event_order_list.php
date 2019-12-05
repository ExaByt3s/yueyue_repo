<?php
/**
 * ���� �����б�
 *
 * @author willike
 * @since 2015/11/11
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// ȫ�� all ������ unpaid ��ȷ�� tbc ��ǩ�� checkin �����  completed �ѹر� closed
$trade_type = $client_data['data']['param']['trade_type'];   // ��������
$activity_id = $client_data['data']['param']['activity_id'];   // �ID
$stage_id = $client_data['data']['param']['stage_id'];   // ����ID
if (empty($user_id) || empty($activity_id) || empty($stage_id)) {
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
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$trade_data = $mall_order_obj->get_order_list_by_activity_stage($activity_id, $stage_id, $status, false, '', 'lately_time DESC', $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$status' => $status,
        '$limit_str' => $limit_str,
        '$trade_data' => $trade_data,
    );  // for test
    return $cp->output($options);
}
$activity_info = array();
if (!empty($trade_data[0])) {
    $trade_activity = $trade_data[0];
    $activity_info = array(
        'type_name' => $trade_activity['type_name'],
        'activity_id' => $trade_activity['activity_id'],
        'title' => $trade_activity['activity_name'],
        'cover' => yueyue_resize_act_img_url($trade_activity['activity_images'], 260),
        'name' => $trade_activity['stage_title'],
        'time' => date('Y-m-d', $trade_activity['service_start_time']),
        'period' => date('Y-m-d H:i', $trade_activity['service_start_time']) . '��' . date('Y-m-d H:i', $trade_activity['service_end_time']),
        'sign' => $trade_activity['is_special'] == 1 ? '�ٷ�' : '',
    );
} else {
    $mall_api_obj = POCO::singleton('pai_mall_api_class');
    $activity_result = $mall_api_obj->get_goods_id_screenings_price_max_and_min($activity_id, $stage_id);
    if (empty($activity_result)) {  // ������
        $options['data'] = array(
            'result' => 0,
            'message' => 'û�иû������Ϣ',
            'activity' => array(),
            'list' => array(),
        );
        return $cp->output($options);
    }
    $activity_info = array(
        'title' => '[Լ�]' . $activity_result['activity_name'],
        'name' => $activity_result['screenings_name'],
        'period' => date('Y-m-d H:i', $activity_result['time_s']) . '��' . date('Y-m-d H:i', $activity_result['time_e']),
        'prices' => '��' . $activity_result['min_price'], //  . '-' . $activity_result['max_price'],
        'unit' => '/�� ��',
        'attend_str' => '�ѱ������� ',
        'attend_num' => intval($activity_result['has_join']),
        'total_num' => $activity_result['total_num'],
    );
}
$trade_list = array();
foreach ($trade_data as $value) {
    $status = $value['status'];  // ����״̬
    $status_str = $value['status_str'];  // ����״̬����
    $is_seller_comment = $value['is_seller_comment'];  // �û��Ƿ����� , 1 ������
    if ($status == 8 && $is_seller_comment == 0) {  // �����,δ����
        $status = 10;
        $status_str = '������';
    }
    $original_amount = $value['original_amount']; // ������۸� $value['original_subtotal']; // ��ԭʼ�۸�
    $show_price = 'ʵ������' . sprintf('%.2f', $value['pending_amount']);  // ��ʾ���
    $trade_info = array(
        'order_sn' => $value['order_sn'],
        'goods_id' => $value['activity_id'],  // �ID
        'buyer_name' => $value['buyer_user_name'],  // �������
        'prime_prices' => '��' . $value['prime_prices'] . ' /��',  // ����
        'standard' => $value['prices_spec'],  // �������
        'status' => $status,
        'status_str' => $status_str,
        'price_str' => $show_price, // ������ʾ�ļ۸�
        'cost' => $value['total_amount'], // ���
        'original' => $original_amount, // ԭ�۽��
        'attend' => '�� ' . $value['stock_num_total'] . '��',
        'link' => 'yueseller://goto?user_id=' . $user_id . '&order_sn=' . $value['order_sn'] . '&pid=1250022&type=inner_app', // ��ת��������
        'action' => interface_trade_seller_action($status, $is_seller_comment, $version),
    );
    $trade_list[] = $trade_info;
}
$num_result = $mall_order_obj->get_order_number_by_stage_for_seller($user_id, $activity_id, $stage_id);
if ($location_id == 'test2') {
    $options['data'] = $num_result;
    return $cp->output($options);
}
$options['data'] = array(
    'result' => 1,
    'summary' => $num_result,  // ����
    'activity' => $activity_info,
    'list' => $trade_list,
);
return $cp->output($options);




