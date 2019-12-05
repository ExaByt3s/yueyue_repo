<?php

/**
 * ���� ����
 *
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID
if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '���׺�Ϊ��');
    return $cp->output($options);
}
$rating = intval($client_data['data']['param']['rating']);   // ���۵ȼ�
if (empty($rating)) {
    $options['data'] = array('result' => -1, 'message' => '����Ϊ��');
    return $cp->output($options);
}
$goods_id = $client_data['data']['param']['goods_id'];   // ��ƷID
if (empty($goods_id)) {
    $options['data'] = array('result' => -1, 'message' => '��ƷIDΪ��');
    return $cp->output($options);
}
$content = $client_data['data']['param']['content'];   // ��������
$is_anon = $client_data['data']['param']['is_anon'];   // �Ƿ�����

$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
$order_info = $mall_order_obj->get_order_full_info($order_sn);

$data = array(
    'from_user_id' => $user_id, // �������û�ID
    'to_user_id' => $order_info['buyer_user_id'],
    'order_id' => $order_info['order_id'],
    'goods_id' => $goods_id,
    'overall_score' => $rating,
    'comment' => empty($content) ? 'û����д��������!' : $content,
    'is_anonymous' => intval($is_anon)
);
$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
$result = $mall_comment_obj->add_buyer_comment($data);  // �������

$options['data'] = $result;
return $cp->output($options);