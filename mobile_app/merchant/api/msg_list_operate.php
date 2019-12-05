<?php

/**
 * ��Ϣ ���� ( ɾ�� ) 
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-12
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = intval($client_data['data']['param']['user_id']);  // �û�ID
$receiver_id = intval($client_data['data']['param']['receiver_id']);  // �����û�ID
$operate = $client_data['data']['param']['operate'];  // ����
if (empty($user_id) || empty($receiver_id) || empty($operate)) {
    $options['data'] = array(
        'result' => -1,
        'message' => 'ȱ�ٲ���!'
    );
    return $cp->output($options);
}

$client = new GearmanClient();
$client->addServer('172.18.5.216', 13200);
$client->setTimeout(5000); // ���ó�ʱ

if ($operate == 'del') {
    // ɾ�������¼
    $json = array(
        'type' => 'listdel',
        'list_id' => 'yueseller/' . $user_id,
        'del_user_id' => strval($receiver_id),
    );
}
$result_json = $client->do('chatlog', json_encode($json)); // ִ�в���
if ($location_id == 'test') {
    var_dump($json, $result_json);
    exit;
}
$result_json = trim($result_json);
$result = json_decode($result_json, TRUE);
$options['data'] = array(
    'result' => 0,
    'message' => 'Failed to delete!'
);
if ($result['code'] == 0) {
    // ɾ���ɹ�
    $options['data'] = array(
        'result' => 1,
        'message' => 'Removed Successfully!'
    );
}
return $cp->output($options);

