<?php

/**
 * ������Ʒ Ԥ���ӿ�
 * 
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-9-11
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = $client_data['data']['param']['goods_id'];   // ��Ʒ���
list($goods_id, $direct_order_id) = explode('_', $goods_id);   // ���� ɨ�������µ�
$request_platform = $client_data['data']['param']['request_platform'];   // ����ƽ̨ ( for ����web 2015-7-29 )
$version = $client_data['data']['version'];  // �汾
$os_type = $client_data['data']['os_type'];  // ����

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->show_goods_data_for_temp($goods_id);
if ($location_id == 'test') { //for debug
    $options['data'] = $goods_result;
    return $cp->output($options);
}
if ($goods_result['result'] != 1 && $goods_result['result'] != -3) {
    return $cp->output(array('data' => array()));
}
$goods = $goods_result['data'];
if (empty($goods)) {
    return $cp->output(array('data' => array()));
}
// ��ȡ��Ʒ����
$goods_property = interface_get_goods_property($goods['system_data'], $goods['goods_data']['location_id']);
$service = $goods_property['service'];
$guide = $goods_property['guide'];
$menu = $goods_property['menu'];;  // ��������
$recommend = $goods_property['recommend']; // �Ƽ�
$guide_img = $goods_property['guide_img'];  // ����
$preview = array(); // ͼƬ
foreach ($goods['goods_data']['img'] as $value) {
    $img_url = $value['img_url'];
    $preview[] = array(
        'thumb' => yueyue_resize_act_img_url($img_url, '640'), // ����ͼ
        'original' => yueyue_resize_act_img_url($img_url), // ԭͼ
    );
}
$goods_user_id = $goods['goods_data']['user_id'];

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($goods_user_id, 2);  // ��ȡ�û���Ϣ
if ($location_id == 'test1') { //for debug
    $options['data'] = $user_result;
    return $cp->output($options);
}
$profile = $user_result['seller_data']['profile'][0];
$type_id_arr = explode(',', $profile['type_id']);  // �û���֤  31 ģ�� 40 ��Ӱʦ
$user_name = $profile['name'];  // �̼�����
$user_avatar = $profile['avatar']; // get_user_icon($goods_user_id, 86, TRUE); // $profile['avatar'];  // ͷ��

$standard = array();  // ���
foreach ($goods['prices_data_list'] as $value) {
    $prices = floatval($value['value']);
    if ($prices <= 0) {
        continue;
    }
    $standard[] = array(
        'id' => $value['id'],
        'name' => $value['name'],
        'value' => sprintf('%.2f', $prices),
        'original' => '',
        'num' => $value['num']
    );
    $min = ($prices < $min) ? $prices : (empty($min) ? $prices : $min);  // ��С�۸�
    $max = ($prices > $max) ? $prices : (empty($max) ? $prices : $max);   // ���۸�
}
// �۸�Χ
$range = empty($max) ? '' : ( $min == $max ? $max : sprintf('%.2f', $min) . 'Ԫ ��');

$score = $goods['goods_data']['average_score']; // �ۺ�����
$score = intval($score) <= 0 ? 5 : $score;
// ��Ʒ����
$contents = trim($goods['default_data']['content']['value']);
$contents = interface_content_replace_pics($contents, 640);  // �滻ͼƬ��С
if (!empty($contents) && $request_platform != 'web') {
    $contents = interface_content_to_ubb($contents);
}

$is_show = intval($goods['goods_data']['is_show']);  // ���¼����
$type_id = intval($goods['goods_data']['type_id']);  // ��Ʒ����
$type_name = $task_goods_obj->get_goods_typename_for_type_id($type_id);  // ��ȡ��������
$type_name = empty($type_name) ? '' : '[' . $type_name . '] ';

$goods_info = array(
    'goods_id' => intval($goods['goods_data']['goods_id']),
    'is_show' => $is_show, // �Ƿ�����: 1 ���� 2 �¼�
    'show_str' => '��ƷԤ��',
    // ���� �ֲ�ͼƬ
    'preview' => $preview,
    'zoom' => 'yueyue://goto?user_id=' . $user_id . '&pid=1250026&type=inner_app', // ����Ŵ�
    'title' => $type_name . preg_replace('/&#\d+;/', '', $goods['default_data']['titles']['value']),
    'prices' => '��' . (empty($range) ? $goods['default_data']['prices']['value'] : $range), // �۸�����
    'original_prices' => '', // ԭʼ�۸�
    'favor' => array(
        'title' => '�ղ�',
        'value' => '0', // 1 ���ղ�
    ),
    // ���
//    'standard' => $standard,
    'promise_title' => '��������',
    'promise' => $service, // ��������
    'user' => array(
        'user_id' => $goods_user_id,
        'name' => $user_name,
        'avatar' => $user_avatar,
        'homepage' => array(
            'title' => '�̼���ҳ',
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_user_id . '&pid=1220103&type=inner_app',
        ),
    ),
    'business' => array(
        'merit' => array('title' => '����', 'value' => strval($score > 5 ? 5 : ( $score < 0 ? 0 : $score))), // �ۺ�����
        'totaltrade' => array('title' => '���״���', 'value' => strval($goods['goods_data']['statistical']['bill_pay_num'])), // ���״���
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_user_id . '&goods_id=' . $goods['goods_data']['goods_id'] . '&pid=1220075&type=inner_app', // ��Ʒ����
    ),
    'profile_type' => $type_id, // ģ�ط���/��Ӱ��ѵ
    // �û�����
    'property' => array(),
    // ͼ������
    'detail' => array(
        'title' => $goods['default_data']['content']['name'],
        'value' => $contents,
    ),
);
$att_data = $profile['att_data'];
$seller_property = interface_get_seller_property($profile['att_data']);  // ��ȡ�û�����
foreach ($seller_property as $v) {
    $p_type_id = $v['type_id'];
    if ($p_type_id == $type_id) {
        $goods_info ['property'] = array($v);
        break;
    }
}

// for web
if ($request_platform == 'web') {
    $goods_info['standard'] = $standard;
}

// Լ��ʳ
if ($type_id == 41) {
    $goods_info['recommend'] = array(
        'title' => '�����Ƽ�ԭ��',
        'value' => $recommend,
    );
    $goods_info['menu'] = array(
        'title' => $menu['title'],
        'value' => implode("\r\n", $menu['value']),
    );
    $goods_info['guide'] = array(
        'title' => '������Ϣ',
        'list' => $guide,
        'img' => $guide_img,
    );
}

$options['data'] = $goods_info; //
return $cp->output($options);
