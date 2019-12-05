<?php

/**
 * ��Ϣ�б�
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-12
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = intval($client_data['data']['param']['user_id']);  // �û�ID
$receiver_id = intval($client_data['data']['param']['receiver_id']);  // �����û�ID ( for �����¼ )
$request_time = $client_data['data']['param']['request_time'];  // ����ʱ��( for �����¼ )
if (empty($request_time)) {
    $request_time = time();
}

$limit = $client_data['data']['param']['limit'];  // ��������
list($start, $rows) = explode(',', $limit);
$start = $start * 1 < 0 ? 0 : intval($start);
$rows = $rows * 1 <= 0 ? 10 : intval($rows);
$rows = $rows > 30 ? 30 : $rows;  // һ�λ�ȡ�б��������� 30��
if (empty($user_id)) {
    $options['data']['list'] = array();
    return $cp->output($options);
}
$client = new GearmanClient();
$client->addServer('172.18.5.216', 13200);
$client->setTimeout(5000); // ���ó�ʱ
if (empty($receiver_id)) {
    // ��Ϣ�б�
    $json = array(
        'type' => 'listget',
        'list_id' => 'yueseller/' . $user_id,
    );
} else {
    $point_json = array(
        'type' => 'listpoint',
        'user_id' => strval($receiver_id), // �Է��û�ID
        'list_id' => 'yueseller/' . $user_id,
    );
    $point_result = $client->do('chatlog', json_encode($point_json)); // ��ȡɾ����
    $point_res = json_decode(trim($point_result), TRUE);
    $start_time = 0;
    if($point_res['result'] > 1){
        $start_time = $point_res['result'];  // ��ɾ���㿪ʼ
    }
    // �����¼
    $json = array(
        'type' => 'history',
        'mode' => 'cross',
        'send_user_id' => 'yueseller/' . $user_id,
        'to_user_id' => 'yuebuyer/' . $receiver_id,
        'start_time' => 0,
        'end_time' => $request_time,
        'offset' => $start, // ƫ���� ( count < 0 ���� )
        'count' => -$rows, // ����ȡ����
    );
}
$history_json = $client->do('chatlog', json_encode($json)); // ��ȡ��ʷ��¼
if ($location_id == 'test') {
    var_dump($json, $history_json);
    exit;
}
$history_list = json_decode(trim($history_json), TRUE);
if ($result['code'] != 0) {
    $options['data'] = array(
        'list' => array(),
        'message' => 'Failed to Search!',
    );
    return $cp->output($options);
}
$list = $history_list['message'];
$new_list = array();
foreach ($list as $v) {
    // ��ΪЭ���� Ĭ��gbkתutf8, ������Ҫ������תgbk����Э�� ( ������ )
    $new_list[] = mb_convert_encoding($v, 'gbk', 'utf8');
}

$options['data']['list'] = $new_list;
return $cp->output($options);
