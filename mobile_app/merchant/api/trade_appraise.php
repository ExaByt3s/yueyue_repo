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

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID
$order_sn = $client_data['data']['param']['order_sn'];   // ����ID

if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '���׺�Ϊ��');
    $cp->output($options);
    exit;
}
$rating = $client_data['data']['param']['rating'];   // ���۵ȼ�
if (empty($rating)) {
    $options['data'] = array('result' => -1, 'message' => '����Ϊ��');
    $cp->output($options);
    exit;
}
$goods_id = $client_data['data']['param']['goods_id'];   // ��ƷID
$content = $client_data['data']['param']['content'];   // ��������
$is_anon = $client_data['data']['param']['is_anon'];   // �Ƿ�����

$api_obj = POCO::singleton('pai_mall_api_class');   // ʵ�����̼ҽ��׶���
$order_info = $api_obj->api_get_order_full_info($order_sn);

$data = array(
    'from_user_id' => $user_id, // �������û�ID
    'to_user_id' => $order_info['buyer_user_id'],
    'order_id' => $order_info['order_id'],
    'goods_id' => $goods_id,
    'overall_score' => $rating,
    'comment' => empty($content) ? 'û����д��������!' : $content,
    'is_anonymous' => intval($is_anon)
);

$result = $api_obj->api_add_buyer_comment($data);  // �������

$options['data'] = $result;
$cp->output($options);
