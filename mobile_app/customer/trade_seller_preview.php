<?php

/**
 * �̼� ҳ��
 * 
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // �̼�ID
$version = $client_data['data']['version'];  // �汾

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->show_seller_data_for_temp($seller_user_id);  // ��ȡ�̼��û���Ϣ
if (empty($user_result)) {
    $options['data'] = array();
    return $cp->output($options);
}
if ($location_id == 'test') {
    $options['data'] = array(
        '$version' => $version,
        '$user_result' => $user_result,
    );
    return $cp->output($options);
}
//$user = $user_result['seller_data'];
$profile = $user_result['seller_data']['profile'][0];
$type_id_arr = explode(',', $profile['type_id']);  // �û���֤   3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ
$profile_info = array();  // ���
foreach ($profile['default_data'] as $value) {
    $profile_info[$value['key']] = $value['value'];
}
$introduce = interface_content_strip($profile_info['introduce']);  // ���˽���
$introduce_more = (strrpos($introduce, '...', -3) === false) ? 0 : 1; // �Ƿ��и�����
// �ۺ�����
$score = $profile['average_score'];
$score = intval($score) <= 0 ? 5 : $score;
$location = get_poco_location_name_by_location_id($profile_info['location_id']);
$user_info = array(
    'user_id' => $profile['user_id'], // �û�ID
    'cover' => yueyue_resize_act_img_url($profile['cover'], '640'), // ����ͼ
    'avatar' => get_seller_user_icon($profile['user_id']), // $profile['avatar'], // ͷ��
    'name' => $profile['name'],
    'type_id' => $profile['type_id'], // ��֤
//    'sex' => $profile['sex'],
    'introduce' => trim($introduce),
    'introduce_more' => $introduce_more,
    'detail' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220111&type=inner_app', // ��������
    'favor' => array(
        'title' => '�ղ�',
        'value' => '0', // 1 ���ղ�
    ),
    'location' => $location . '    ID: ' . $profile['user_id'],
    // ����
    'property' => array(),
    // ������Ϣ
    'business' => array(
        'merit' => array('title' => '�ۺ�����', 'value' => strval($score > 5 ? 5 : ( $score < 0 ? 0 : $score))), // �ۺ�����
        'totaltrade' => array('title' => '���״���', 'value' => strval($user_result['seller_data']['bill_pay_num'])), // ���״���
        // 'totaltrade' => array('title' => '���״���', 'value' => $profile['review_times']), // ���״���
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
    ),
);

// 3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ
$user_info['property'] = interface_get_seller_property($profile['att_data']);

$options['data'] = $user_info;
return $cp->output($options);

