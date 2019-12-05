<?php

/**
 * ��Ʒ ����
 * 
 * @since 2015-6-26
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input(array('be_check_token' => false));

$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// on_sell �ϼ�  off_sell �¼�
$operate = $client_data['data']['param']['operate'];   // ��������
$goods_id = $client_data['data']['param']['goods_id'];   // ��ƷID
if (empty($user_id) || empty($goods_id)) {
    $result = array('result' => -4, 'message' => '��ƷIDΪ��');
    $cp->output(array('data' => $result));
    exit;
}
// 1 �ϼ� 2�¼�
$status_arr = array(
    'on_sell' => 1,
    'off_sell' => 2,
);
if (!isset($status_arr[$operate])) {
    $result = array('result' => -5, 'message' => '�Ƿ�����');
    $cp->output(array('data' => $result));
    exit;
}
$status = $status_arr[$operate];

$api_obj = POCO::singleton('pai_mall_api_class');   // ʵ��������
$result = $api_obj->api_user_change_goods_show_status($goods_id, $status, $user_id);
$cp->output(array('data' => $result));
