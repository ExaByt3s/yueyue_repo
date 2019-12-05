<?php

/**
 * �̼���ҳ
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // ��ȡ�û���Ϣ
if (empty($user_result)) {  // ������
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
// ����λ��
if (version_compare($version, '1.3', '>')) {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']);
} else {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']) . '    ID: ' . $profile['user_id'];
}
// �ۺ�����
$score = $profile['average_score'];
$score = intval($score) <= 0 ? 5 : $score;
$user_info = array(
    'user_id' => $profile['user_id'], // �û�ID
    'cover' => yueyue_resize_act_img_url($profile['cover'], '640'), // ����ͼ
    'avatar' => get_seller_user_icon($profile['user_id'], 165, TRUE), // $profile['avatar'], // ͷ��
    'name' => $profile['name'],
    'type_id' => $profile['type_id'], // ��֤
//    'sex' => $profile['sex'],
    'introduce' => trim($introduce),
    'detail' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250005&type=inner_app', // ��������
    'character' => 'ID: ' . $profile['user_id'],
    'location' => str_replace(' ', '��', $location),
    // ����
    'property' => array(),
    // ������Ϣ
    'business' => array(
        'merit' => array('title' => '�ۺ�����', 'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score))), // �ۺ�����
        // ���״���
        'totaltrade' => array(
            'title' => '���״���',
            'value' => $user_result['seller_data']['bill_pay_num'] . '��', // ��ɽ����� ( $profile['review_times'] ���۴��� )
            'request' => 'yueseller://goto?user_id=' . $profile['user_id'] . '&pid=1250009&type=inner_app', // �û������б�
        ),
    ),
    'showtitle' => '�����б�',
    // ��Ʒչʾ
    'showcase' => array(),
    // ������Ʒ
    'morecase' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250026&goods_type=all&type=inner_app',
    // ����
    'share' => array(),
);

// ��ȡ�û�������Ϣ
$share_result = $seller_obj->get_share_text($user_id);
$user_info['share'] = $share_result;
// 3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ
$user_info['property'] = interface_get_seller_property($profile['att_data']);
// ��Ʒ�б�
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
// status״̬ 0δ���,1ͨ��,2δͨ��,3ɾ��;show��/�¼� 1�ϼ�,2�¼�;type_id��Ʒ����,keyword�����ؼ���
$data = array(
    'status' => 1,
    'show' => 1,
    'type_id' => 0,
    'keyword' => '',
);
$goods_list = $task_goods_obj->user_goods_list($user_id, $data, false, 'goods_id DESC', '0,6', '*');
if (version_compare($version, '3.3', '>')) {
    // ���Ϣ
    $data = array(
        'action_type' => 1,
    );
    $event_list = $task_goods_obj->user_goods_list($user_id, $data, false, 'goods_id DESC', '0,6', '*');
    if ($location_id == 'test2') {
        $options['data'] = array(
            '$user_id' => $user_id,
            '$data' => $data,
            '$event_list' => $event_list,
        );
        return $cp->output($options);
    }
    $goods_list = array_merge($goods_list, $event_list);
}
if ($location_id == 'test1') {
    $options['data'] = array(
        '$seller_user_id' => $seller_user_id,
        '$data' => $data,
        '$goods_list' => $goods_list,
    );
    return $cp->output($options);
}
if (count($goods_list) < 6 && version_compare($version, '1.1') >= 0) {
    // �Ƿ���ʾ �鿴���� ����
    unset($user_info['morecase']);
}
$promotion_obj = POCO::singleton('pai_promotion_class');  // ����
$showcase = array();
foreach ($goods_list as $value) {
    $type_id = $value['type_id'];  // �����
    $price_str = $value['prices'];
    $prices_list = unserialize($value['prices_list']);
    $pro_prices_list = $price_str;  // �����۸�(for ����)
    if (!empty($prices_list)) {
        $tmp = 0;
        $pro_prices_list = array();
        foreach ($prices_list as $k => $price) {
            if (intval($price) <= 0) {
                continue;
            }
            $pro_prices_list[] = array(
                'prices_type_id' => $k, //����
                'goods_prices' => $price, //����
            );
            if ($tmp > 0 && $price > $tmp) {
                continue;
            }
            $tmp = $price;
        }
        if ($tmp > 0) {
            $price_str = sprintf('%.2f', $tmp) . 'Ԫ ��';
        }
    }
    $goods_id = $value['goods_id']; // ��ƷID
    $link = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250007&type=inner_app'; // ��������
    if ($type_id == 42) {
        // �����
        $link = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
    }
    $case_info = array(
        'goods_id' => $value['goods_id'],
        'type_id' => $type_id,
        'title' => $value['titles'],
        'prices' => '��' . $price_str,
        'pic' => $value['images'],
        'link' => $link, // ��������
    );
    if (!empty($pro_prices_list) && version_compare($version, '1.2', '>')) {
        $promotion = interface_get_goods_promotion($user_id, $value['goods_id'], $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '��ʡ: ' . $promotion['abate'];
            // ��� ������Ϣ
            $case_info = array_merge($case_info, $promotion);
        }
    }
    if (version_compare($version, '1.3', '<')) {
        $user_info['showcase'][] = $case_info;
        continue;
    }
    $add_time = $value['add_time'];  // ���ʱ��
    $showcase[$add_time] = $case_info;
}
if (version_compare($version, '1.3', '>')) {
    krsort($showcase);
    $user_info['showcase'] = array_slice($showcase, 0, 6);
}

$options['data'] = $user_info;
return $cp->output($options);

