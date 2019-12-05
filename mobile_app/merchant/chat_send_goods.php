<?php

/**
 * ���촰�� ���ͷ���
 *
 * @author heyaohua
 * @editor willike <chenwb@yueus.com>
 * @since 2015/7/23
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$send_user_id = $client_data['data']['param']['user_id'];
$send_user_role = $client_data['data']['param']['send_user_role'];
$to_user_id = $client_data['data']['param']['to_user_id'];
$to_user_role = $client_data['data']['param']['to_user_role'];
$goods_id = $client_data['data']['param']['goods_id'];

if (empty($send_user_role)) {
    $send_user_role = 'yueseller';
}
if (empty($to_user_role)) {
    $to_user_role = 'yuebuyer';
}

if (empty($goods_id) || empty($to_user_id)) {
    $options['data'] = array(
        'result' => 0,
        'message' => '��Ϣ������',
    );
    return $cp->output($options);
}
// ��ȡ��Ʒ��Ϣ
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->user_get_goods_info($goods_id, $send_user_id);
if ($location_id == 'test1') {
    $options['data'] = $goods_result;
    return $cp->output($options);
}
if ($goods_result['result'] != 1) {
    $options['data'] = array(
        'result' => -1,
        'message' => '����Ʒδ�ϼ�',
    );
    return $cp->output($options);
}
$goods = $goods_result['data'];
$goods_type_id = intval($goods['goods_data']['type_id']);  // ��Ʒ����
$title = $goods['goods_data']['titles'];  // ��Ʒ����
$prices = $goods['goods_data']['prices'];  // ��Ʒ�۸�
$prices = sprintf('%.2f', $prices); // ������λС��
$images = $goods['goods_data']['images'];  // ��ƷͼƬ
$images = yueyue_resize_act_img_url($images, 640);

/// ios 3.0.10  android 3.0.0
$client = new GearmanClient();
$client->addServer('172.18.5.211', 9830);
$client->setTimeout(5000); // ���ó�ʱ
$json = array(
    'pocoid' => 'yuebuyer/' . $to_user_id, // ������
);
$user_version = $client->do('get_client_info', json_encode($json)); // ��ȡ������app�汾��
$version_arr = json_decode(trim($user_version), TRUE);
$card_style = 2;
if (!empty($version_arr['appver']) && version_compare($version_arr['appver'], '3.0.10') > 0) {
    // ������ >= 3.1.0 ����Ƭ4����
    $card_style = 4;
}

$type_data = array(
    2 => array(
        'media_type' => 'card',
        'card_style' => '2',
        'card_text1' => $title . "\r\n" . '��' . $prices,
        'card_title' => '�鿴��������',
    ),
    3 => array(
        'media_type' => 'card',
        'card_style' => '3',
        'card_text1' => $title,
        'file_small_url' => $images,
        'card_text2' => '��' . $prices,
    ),
    4 => array(
        'media_type' => 'merchandise',
        'card_style' => '3',
        'card_text1' => $title,
        'file_small_url' => $images,
        'card_text2' => '��' . $prices,
    ),
);

$to_send_data = $type_data[4];  // ������
$send_data = isset($type_data[$card_style]) ? $type_data[$card_style] : $type_data[2];  // ������

if ($location_id == 'test') {
    $options['data'] = array(
        'version_arr' => $version_arr,
        'param' => $client_data['data']['param'],
        'send_data' => $send_data,
        'to_send_data' => $to_send_data,
    );
    return $cp->output($options);
}
$pai_send_obj = POCO::singleton('pai_information_push');
$pai_send_obj->card_send_for_goods($send_user_id, $send_user_role, $to_user_id, $to_user_role, $send_data, $to_send_data, $goods_id, $goods_type_id);

$options['data'] = array(
    'result' => 1,
    'message' => '���ͳɹ�',
);
return $cp->output($options);

