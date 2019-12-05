<?php

/**
 * ���� ����
 * 
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */

include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input(array('be_check_token' => false));

$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// �رն��� close,ɾ������ delete
$operate = $client_data['data']['param']['operate'];   // ��������
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID
if (empty($user_id) || empty($order_sn)) {
    $result = array('result' => -4, 'message' => '���׺�Ϊ��');
    $cp->output(array('data' => $result));
    exit;
}
$api_obj = POCO::singleton('pai_mall_api_class');   // ʵ�����̼ҽ��׶���
switch ($operate) {
    case 'accept':   // ���ܶ���
        break;
    case 'refuse':  // �ܾ�����
        break;
    case 'sign':  // ǩ������
        break;
    case 'close':  // �رն���
        $result = $api_obj->api_close_order_for_buyer($order_sn, $user_id);
        break;
    case 'delete':  // ɾ������
        $result = $api_obj->api_del_order_for_buyer($order_sn, $user_id);
        break;
    default:
        $result = array('result' => -4, 'message' => '��������Ϊ��');
        break;
}
$cp->output(array('data' => $result));
