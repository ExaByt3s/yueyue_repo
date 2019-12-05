<?php

/**
 * ���� ����
 * 
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID
if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '���׺�Ϊ��');
    return $cp->output($options);
}
// �������۷��� overall_score,��Ʒ���Ϸ��� match_score,̬������ manner_score,�������� quality_score
$overall_score = $client_data['data']['param']['overall_score'];   // �������۷���
$match_score = $client_data['data']['param']['match_score'];   // ��Ʒ���Ϸ���
$manner_score = $client_data['data']['param']['manner_score'];   // ̬������
$quality_score = $client_data['data']['param']['quality_score'];   // ��������
if (empty($overall_score) || empty($match_score) || empty($manner_score) || empty($quality_score)) {
    $options['data'] = array('result' => -1, 'message' => '����Ϊ��');
    return $cp->output($options);
}
$goods_id = $client_data['data']['param']['goods_id'];   // ��ƷID
$content = $client_data['data']['param']['content'];   // ��������
$is_anon = $client_data['data']['param']['is_anon'];   // �Ƿ�����

$mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
$order_info = $mall_order_obj->get_order_full_info($order_sn);

$data = array(
    'from_user_id' => $user_id, // �������û�ID
    'to_user_id' => $order_info['seller_user_id'],
    'order_id' => $order_info['order_id'],
    'goods_id' => $goods_id,
    'overall_score' => $overall_score, // �������۷���
    'match_score' => $match_score, // ��Ʒ���Ϸ���
    'manner_score' => $manner_score, // ̬������
    'quality_score' => $quality_score, // ��������
    'comment' => empty($content) ? 'û����д��������!' : $content,
    'is_anonymous' => intval($is_anon)
);
$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
$result = $mall_comment_obj->add_seller_comment($data);  // �����̼�

$options['data'] = $result;
return $cp->output($options);
