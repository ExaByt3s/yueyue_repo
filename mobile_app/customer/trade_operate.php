<?php

/**
 * ���� ����
 * 
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// �رն��� close,ɾ������ delete
$operate = $client_data['data']['param']['operate'];   // ��������
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID
if (empty($user_id) || empty($order_sn)) {
    $result = array('result' => -4, 'message' => '���׺�Ϊ��');
    $cp->output(array('data' => $result));
    exit;
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
switch ($operate) {
    case 'accept':   // ���ܶ���
        break;
    case 'refuse':  // �ܾ�����
        break;
    case 'sign':  // ǩ������
        break;
    case 'close':  // �رն���
        $result = $mall_order_obj->close_order_for_buyer($order_sn, $user_id);
        break;
    case 'delete':  // ɾ������
        $result = $mall_order_obj->del_order_for_buyer($order_sn, $user_id);
        break;
    default:
        $result = array('result' => -4, 'message' => '��������Ϊ��');
        break;
}
$cp->output(array('data' => $result));
