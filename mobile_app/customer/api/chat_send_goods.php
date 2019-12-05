<?php

/**
 * ���촰�� ������Ʒ
 * 
 * @author heyaohua
 * @editor willike <chenwb@yueus.com>
 * @since 2015/7/23
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$send_user_id = $client_data['data']['param']['user_id'];
$send_user_role = $client_data['data']['param']['send_user_role'];
$to_user_id = $client_data['data']['param']['to_user_id'];
$to_user_role = $client_data['data']['param']['to_user_role'];
$goods_id = $client_data['data']['param']['goods_id'];

if (empty($send_user_role)) {
    $send_user_role = 'yuebuyer';
}
if (empty($to_user_role)) {
    $to_user_role = 'yueseller';
}

if (empty($goods_id) || empty($to_user_id)) {
    $options['data'] = array(
        'result' => 0,
        'message' => '��Ϣ������',
    );
    $cp->output($options);
    exit;
}
// ��ȡ��Ʒ��Ϣ
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->get_goods_info_by_goods_id($goods_id);
if ($location_id == 'test1') {
    $options['data'] = $goods_result;
    $cp->output($options);
    exit;
}
if ($goods_result['result'] != 1) {
    $options['data'] = array(
        'result' => -1,
        'message' => '����Ʒ���¼�',
    );
    $cp->output($options);
    exit;
}
$goods_user_id = $goods_result['data']['goods_data']['user_id'];  // ��Ʒ����
if ($goods_user_id != $to_user_id) {
    $options['data'] = array(
        'result' => -2,
        'message' => '�ǵ�ǰ������Ʒ',
    );
    $cp->output($options);
    exit;
}
$title = $goods_result['data']['goods_data']['titles'];  // ��Ʒ����
$prices = $goods_result['data']['goods_data']['prices'];  // ��Ʒ�۸�

// android 1.0.0  ios 1.0.1
$client = new GearmanClient();
$client->addServer('172.18.5.211', 9830);
$client->setTimeout(5000); // ���ó�ʱ
$json = array(
    'pocoid' => 'yueseller/' . $to_user_id, // �̼�
);
$user_version = $client->do('get_client_info', json_encode($json)); // ��ȡ�汾��
$version_arr = json_decode(trim($user_version), TRUE);
$card_style = 2;
if (!empty($version_arr['appver']) && version_compare($version_arr['appver'], '1.0.1') > 0) {
    // �̼Ұ� > 1.0.1 ����Ƭ3����
    $card_style = 3;
}

$send_data['media_type'] = 'card';
$send_data['card_style'] = strval($card_style);
$send_data['card_text1'] = '��ѯ:' . $title . "\r\n" . '�۸�' . $prices;
//$send_data['card_text2'] = '��ѯ:' . $title . '�۸�' . $prices;
$send_data['card_title'] = '�鿴��������';

$to_send_data['media_type'] = 'card';
$to_send_data['card_style'] = strval($card_style);
$to_send_data['card_text1'] = '��ѯ:' . $title . "\r\n" . '�۸�' . $prices;
//$to_send_data['card_text2'] = '��ѯ:' . $title . '�۸�' . $prices;
$to_send_data['card_title'] = '�鿴��������';

if ($location_id == 'test') {
    $options['data'] = array(
        'version_arr' => $version_arr,
        'param' => $client_data['data']['param'],
        'data' => $to_send_data,
    );
    $cp->output($options);
    exit;
}
$pai_send_obj = POCO::singleton('pai_information_push');
$pai_send_obj->card_send_for_goods($send_user_id, $send_user_role, $to_user_id, $to_user_role, $send_data, $to_send_data, $goods_id);

$options['data'] = array(
    'result' => 1,
    'message' => '���ͳɹ�',
);
return $cp->output($options);

