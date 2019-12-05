<?php

/**
 * ���� ����
 *
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// ���ܶ��� accept,�ܾ����� refuse,ǩ������ sign,�رն��� close,ɾ������ delete
$operate = $client_data['data']['param']['operate'];   // ��������
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID
$code_sn = $client_data['data']['param']['code_sn'];   // ǩ���� ( ֻ��ǩ���������� )
if (empty($user_id) || empty($order_sn)) {
    $result = array('result' => -4, 'message' => '���׺�Ϊ��');
    return $cp->output(array('data' => $result));
}
if ($operate == 'sign' && empty($code_sn)) {
    $result = array('result' => -4, 'message' => 'ǩ����Ϊ��');
    return $cp->output(array('data' => $result));
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
switch ($operate) {
    case 'accept':   // ���ܶ���
        $result = $mall_order_obj->accept_order($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '�������ܳɹ�';
        }
        break;
    case 'refuse':  // �ܾ�����
        $result = $mall_order_obj->refuse_order($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '�����ܾ��ɹ�';
        }
        break;
    case 'sign':  // ǩ������
        $result = $mall_order_obj->sign_order($code_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '����ǩ���ɹ�';
        }
        break;
    case 'close':  // �رն���
        $result = $mall_order_obj->close_order_for_seller($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '�����رճɹ�';
        }
        break;
    case 'delete':  // ɾ������
        $result = $mall_order_obj->del_order_for_seller($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '����ɾ���ɹ�';
        }
        break;
    default:
        $result = array('result' => -4, 'message' => '��������Ϊ��');
        break;
}
return $cp->output(array('data' => $result));
