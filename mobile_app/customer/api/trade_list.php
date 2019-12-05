<?php

/**
 * ���� �б�
 * 
 * @since 2015-7-1
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
// ȫ�� all ������ unpaid ��ȷ�� tbc ��ǩ�� checkin �����  completed �ѹر� closed
$trade_type = $client_data['data']['param']['trade_type'];   // ��������
// Ӱ������ studio_rent ,������� life_service,��ױ���� makeup,��Ӱ��ѵ shooting_training,ģ�ط��� model_service,��Ӱ���� photo_service 
$goods_type = $client_data['data']['param']['goods_type'];   // ��Ʒ����
if (empty($user_id)) {
    $cp->output(array('data' => array()));
    exit;
}
$page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
$rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ( $rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    $limit_str = $limit;
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
$goods_arr = array('studio_rent' => 12, 'life_service' => 2, 'makeup' => 3, 'shooting_training' => 5, 'model_service' => 31, 'photo_service' => 40);
$type_id = isset($goods_arr[$goods_type]) ? $goods_arr[$goods_type] : 0; // ĳ��Ʒ��/ȫ��

$api_obj = POCO::singleton('pai_mall_api_class');   // ʵ�����̼ҽ��׶���
$trade_data = $api_obj->api_get_order_list_for_buyer($user_id, $type_id, $status, false, 'order_id DESC', $limit_str, '*');
if ($location_id == 'test') {
    $options['data']['list'] = $trade_data;  // for test 
    $cp->output($options);
    exit;
}
$trade_list = array();
foreach ($trade_data as $value) {
    $order_sn = $value['order_sn'];
    $status = $value['status'];
    $is_buyer_comment = $value['is_buyer_comment'];  // �Ƿ�����
    $status_str = $value['status_str'];
    if ($status == 8 && $is_buyer_comment == 0) {  // δ����
        $status = 10;
        $status_str = '������';
    }
    $seller_user_id = $value['seller_user_id'];
    $trade_info = array(
        'order_sn' => $order_sn,
        'type_id' => $value['type_id'], // ��ƷƷ��
//        'type_name' => $type_arr[$value['type_id']]['name'],
        'title' => get_user_nickname_by_user_id($seller_user_id), // ����, ����������
//        'title' => $value['detail_list'][0]['goods_name'], // ����, ������������
        'desc' => $value['detail_list'][0]['goods_name'], // ����, ��������Ʒ��
        'link' => 'yueyue://goto?user_id=' . $user_id . '&order_sn=' . $order_sn . '&pid=1220106&type=inner_app', // ��ת��������
        'status' => $status, // ״̬
        'status_str' => $status_str, // ״̬����
        'cost' => $value['total_amount'], // ���
        'goods_id' => $value['detail_list'][0]['goods_id'], // ��ƷID
        'thumb' => $value['detail_list'][0]['goods_images'], // Ԥ��ͼ
        'action' => btn_action($value['status'], $is_buyer_comment),
    );
    $trade_list[] = $trade_info;
}
$options['data']['list'] = $trade_list;
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
        0 => '����.pay|ȡ������.close',
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
