<?php

/**
 * ��Ʒ ����
 * 
 * @since 2015-6-26
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
// on_sell �ϼ�  off_sell �¼�
$operate = $client_data['data']['param']['operate'];   // ��������
$goods_id = $client_data['data']['param']['goods_id'];   // ��ƷID
if (empty($user_id) || empty($goods_id)) {
    $result = array('result' => -4, 'message' => '��ƷIDΪ��');
    return $cp->output(array('data' => $result));
}
// 1 �ϼ� 2�¼�
$status_arr = array(
    'on_sell' => 1,
    'off_sell' => 2,
);
if (!isset($status_arr[$operate])) {
    $result = array('result' => -5, 'message' => '�Ƿ�����');
    return $cp->output(array('data' => $result));
}
$status = $status_arr[$operate];

$mall_goods_obj = POCO::singleton('pai_mall_goods_class');   // ʵ��������
$result = $mall_goods_obj->user_change_goods_show_status($goods_id, $status, $user_id);
if ($result['result'] == 1) {  // �޸ĳɹ�
    $result['message'] = $operate == 'on_sell' ? '�ϼܳɹ�' : '�¼ܳɹ�';
}
return $cp->output(array('data' => $result));
