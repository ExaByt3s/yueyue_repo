<?php
/**
 * ����� �б�
 *
 * @author willike
 * @since 2015/11/10
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// ȫ�� all ������ unpaid ��ȷ�� tbc ��ǩ�� checkin �����  completed �ѹر� closed
$trade_type = $client_data['data']['param']['trade_type'];   // ��������
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
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$trade_data = $mall_order_obj->get_activity_list_by_order_for_seller($user_id, $status, false, 'lately_time DESC', $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$status' => $status,
        '$limit_str' => $limit_str,
        '$trade_data' => $trade_data,
    );  // for test
    return $cp->output($options);
}
$activity_message_obj = POCO::singleton('pai_mall_activity_batch_message_class');
$trade_list = array();
foreach ($trade_data as $value) {
    $activity_id = $value['activity_id']; // �ID
    $stage_id = $value['stage_id'];  // ����ID
    $times_result = $activity_message_obj->send_times_left($user_id, $activity_id, $stage_id);
    $send_times = intval($times_result); // Ⱥ������
    $stage_min_price = $value['stage_min_price'];  // ��ͼ�
    $stage_max_price = $value['stage_max_price'];  // ��߼�
    $show_price = '��' . ($stage_min_price > 0 ? $stage_min_price . '-' : '') . ($stage_max_price > 0 ? $stage_max_price : '');  // ��ʾ���
    $trade_info = array(
        'goods_id' => $activity_id, // �ID
        'stage_id' => $stage_id,  // ����ID
        'title' => $value['activity_name'], // �����
        'desc' => date('Y-m-d', $value['service_start_time']) . ' ' . $value['stage_title'], // ��������
        'price_str' => $show_price, // ������ʾ�ļ۸�
        'attend' => '�Ѹ��' . $value['attend_num'] . '/' . $value['stock_num_total'] . '��',
        'thumb' => $value['activity_images'], // Ԥ��ͼ
        'link' => 'yueseller://goto?user_id=' . $user_id . '&activity_id=' . $value['activity_id'] . '&stage_id=' . $value['stage_id'] . '&pid=1250043&type=inner_app', // ��ת�����б�
        'send_link' => 'yueseller://goto?user_id=' . $user_id . '&activity_id=' . $value['activity_id'] .
            '&stage_id=' . $value['stage_id'] . '&send_times=' . $send_times . '&max_len=200' . '&pid=1250046&type=inner_app', // ��ת��������
        'action' => array(
            array('title' => 'Ⱥ��֪ͨ', 'request' => 'mass',),
        ),
    );
    $trade_list[] = $trade_info;
}
$options['data'] = array(
    'list' => $trade_list,
);
return $cp->output($options);