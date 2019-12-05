<?php
/**
 * ���������
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$activity_id = $client_data['data']['param']['goods_id'];   // �ID
$stage_id = $client_data['data']['param']['stage_id'];   // ����ID
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
if (empty($activity_id) || empty($stage_id)) {
    $options['data'] = array('result' => 0, 'message' => '��������ȷ'); //
    return $cp->output($options);
}
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->get_goods_info_by_goods_id($activity_id);
if (empty($goods_result)) {
    $options['data'] = array('result' => -1, 'message' => 'û�иû'); //
    return $cp->output($options);
}
if ($goods_result['result'] != 1 && $goods_result['result'] != -3) {
    return $cp->output(array('data' => $goods_result));
}
$goods = $goods_result['data'];
$prices_data_list = $goods['prices_data_list'];  // �۸��б�
$activity_id = $goods['goods_data']['goods_id'];
$title = '[Լ�] ' . $goods['default_data']['titles']['value'];  // �����
$exhibit = interface_get_goods_showing($prices_data_list);
// ���� ����
$roster = $showing = array();
foreach ($exhibit['all_exhibit'] as $exhibit_list) {
    $exhibit_stage_id = $exhibit_list['stage_id'];
    $exhibit_list['activity_id'] = $activity_id;
    $exhibit_list['title'] = $title;  // �����
    if ($exhibit_stage_id == $stage_id) {
        $roster = $exhibit_list;
    }
    $showing[] = $exhibit_list;
}
if ($location_id == 'test1') {
    $options['data'] = array(
        '$roster' => $roster,
        '$showing' => $showing,
        '$exhibit' => $exhibit,
        '$goods_result' => $goods_result,
    );  // for test
    return $cp->output($options);
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$trade_data = $mall_order_obj->get_order_list_by_activity_stage($activity_id, $stage_id, -1, false, '`status` IN(0,2,8)', 'lately_time DESC', $limit_str);
//$trade_data = $mall_order_obj->get_order_list_of_paid_by_stage($activity_id, $stage_id, false, 'lately_time DESC', $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$status' => $status,
        '$limit_str' => $limit_str,
        '$trade_data' => $trade_data,
    );  // for test
    return $cp->output($options);
}
$attend_list = array();
foreach ($trade_data as $value) {
    $status = $value['status'];  // ����״̬
    if ($status == 7) {  // �ѹر� ������
        continue;
    }
    $status_str = $value['status_str'];  // ����״̬����
    $is_buyer_comment = $value['is_buyer_comment'];  // �û��Ƿ����� , 1 ������
    if ($status == 8 && $is_buyer_comment == 0) {  // �����,δ����
        $status = 10;
        $status_str = '������';
    }
    $trade_info = array(
        'order_sn' => $value['order_sn'],
        'user_id' => $value['buyer_user_id'],
        'name' => $value['buyer_user_name'],  // �������
        'avatar' => yueyue_resize_act_img_url($value['buyer_user_icon'], 64),
        'standard' => $value['prices_spec'],  // ���
        'buy_num' => $value['quantity'] . '��',  // ��������
        'remark' => $value['description'], // ��ע
        'status' => $status,
        'status_str' => $status_str,
    );
    $attend_list[] = $trade_info;
}
$roster['exhibit'] = $attend_list;  // �����б�
$roster['showing'] = $showing;
$options['data'] = $roster; //
return $cp->output($options);