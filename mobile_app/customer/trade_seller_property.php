<?php

/**
 * �̼� ����
 *
 * @since 2015-8-12
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$seller_user_id = intval($client_data['data']['param']['seller_user_id']);  // �̼�ID
$type_id = intval($client_data['data']['param']['type_id']);  // ����ID  ( for web )

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($seller_user_id, 2);  // ��ȡ�û���Ϣ
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$profile = $user_result['seller_data']['profile'][0];
if (empty($profile)) {
    $options['data'] = array('result' => 0, 'message' => 'û�и��û�����');
    return $cp->output($options);
}
$score = $record_result['comment_score'];   // �û�����
$score = empty($score) ? 5 : $score;  // Ĭ��5��
$property = array(
    'type_id' => $profile['type_id'],
    // ������Ϣ
    'business' => array(
        // �ۺ�����
        'merit' => array(
            'title' => '�ۺ�����',
            'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score)),
        ),
        // ���״���
        'totaltrade' => array(
            'title' => '���״���',
            'value' => strval($user_result['seller_data']['bill_pay_num']), // $profile['review_times']), // ���״���
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
        ),
    ),
);

$user_property = interface_get_seller_property($profile['att_data']);
foreach ($user_property as $val) {
    $u_type_id = $val['type_id'];
    $property['type_' . $u_type_id] = $val;
}
// ��ʾ����
$property['show'] = isset($property['type_' . $type_id]) ? 'type_' . $type_id : 'business';

$options['data'] = $property;
return $cp->output($options);

