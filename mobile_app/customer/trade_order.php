<?php

/**
 * �µ��ӿ�
 * 
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = $client_data['data']['param']['goods_id'];  // ��ƷID
$type_id = $client_data['data']['param']['type_id'];  // ���ID
$num = $client_data['data']['param']['num'];  // ����
if (empty($goods_id) || $num < 1) {
    $option['data'] = array('result' => '-1', 'message' => '��Ϣ������');
    $cp->output($options);
    exit;
}
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->get_goods_info_by_goods_id($goods_id);  // ��ȡ��Ʒ����
if ($location_id == 'test') { //for debug
    $options['data'] = $goods_result;
    $cp->output($options);
    exit;
}
if ($goods_result['result'] != 1) {
    $cp->output(array('data' => array('result' => '-2', 'message' => '�޴���Ʒ')));
    exit;
}
$goods_type = array();
foreach ($goods_result['prices_data_list'] as $value) {
    $goods_type[$value['id']] = $value;
}
// �ж� ����Ʒ�Ƿ� �д˹��
if (!isset($goods_type[$type_id])) {
    $cp->output(array('data' => array('result' => '-3', 'message' => '�޴˹��')));
    exit;
}

$detail_list = array(
    'type_id' => $type_id,
    'goods_id' => $goods_id,
    'goods_name' => $goods_result['goods_data']['titles'],
    'prices_type_id' => '',
    'prices_spec' => $goods_type['name'], // ���
    'goods_version' => $goods_result['goods_data']['version'],
    'service_time' => 0,
    'service_location_id' => $goods_result['goods_data']['location_id'],
    'service_address' => '',
    'prices' => '��'.$goods_type['value'],
    'quantity' => $num,
);
//$more_info = array('referer' => '', 'description' => '');
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
$order_result = $mall_order_obj->submit_order($user_id, $detail_list, $more_info = array());
//array('result'=>0, 'message'=>'', 'order_sn'=>'');

$options['data'] = $order_result;
return $cp->output($options);
