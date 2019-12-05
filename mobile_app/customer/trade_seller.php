<?php

/**
 * �̼���ҳ ҳ��
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-6-19
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // �̼�ID
$version = $client_data['data']['version'];  // �汾
$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($seller_user_id, 2);  // ��ȡ�̼��û���Ϣ
if (empty($user_result)) {
    // �̼һ�ȡ��������, ��ʹ������������
    $seller_obj = POCO::singleton('pai_user_class');
    $user_result = $seller_obj->get_user_info($seller_user_id);  // ��ȡ�������û���Ϣ
    $introduce = interface_content_strip($user_result['remark']);  // ���˽���
    // ��ȡ ���״��������ѽ��
    $obj = POCO::singleton('pai_user_data_class');
    $record_result = $obj->get_user_data_info($seller_user_id);
    $score = $record_result['comment_score'];   // �û�����
    $score = empty($score) ? 5 : $score;  // Ĭ��5��
    $avatar = get_user_icon($user_result['user_id']);
    // ����λ��
    if (version_compare($version, '3.3', '>')) {
        $location = get_poco_location_name_by_location_id($user_result['location_id']);
    } else {
        $location = get_poco_location_name_by_location_id($user_result['location_id']) . '    ID: ' . $user_result['user_id'];
    }
    $user_info = array(
        'user_id' => $user_result['user_id'], // �û�ID
        'cover' => yueyue_resize_act_img_url($avatar, '640'), // ����ͼ
        'avatar' => $avatar, // ͷ��
        'name' => $user_result['nickname'], // �ǳ�
        'introduce' => trim($introduce),
        'detail' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220111&type=inner_app', // ��������
        'character' => 'ID: ' . $user_result['user_id'],
        'location' => $location,
        // ����
        'property' => array(),
        // ������Ϣ
        'business' => array(
            'merit' => array('title' => '�ۺ�����', 'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score))), // �ۺ�����
            'totaltrade' => array('title' => '���״���', 'value' => strval($record_result['deal_times'])), // ���״���
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
        ),
        'showtitle' => '����ͼ��',
        // ��Ʒչʾ
        'showcase' => array(),
        // ����
        'share' => $seller_obj->get_share_text($seller_user_id),
    );
    $options['data'] = $user_info;
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
$favor = array(
    'title' => '�ղ�',
    'value' => '0', // 1 ���ղ�
);
if (version_compare($version, '3.2', '>')) {  // 3.2.0 �����ղ�
    $follow_user_obj = POCO::singleton('pai_mall_follow_user_class');
    $favor_res = $follow_user_obj->check_user_follow($user_id, $seller_user_id);
    if (true == $favor_res) {
        $favor = array(
            'title' => '���ղ�',
            'value' => '1', // 1 ���ղ�
        );
    }
}
// �ۺ�����
$score = $profile['average_score'];
$score = intval($score) <= 0 ? 5 : $score;
// ����λ��
if (version_compare($version, '3.3', '>')) {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']);
} else {
    $location = get_poco_location_name_by_location_id($profile_info['location_id']) . '    ID: ' . $profile['user_id'];
}
// ���״���
$trade_num = $user_result['seller_data']['bill_finish_num']; //$user_result['seller_data']['bill_pay_num']; // ���״���
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
    'favor' => $favor, // �ղ�
    'character' => 'ID: ' . $profile['user_id'],
    'location' => str_replace(' ', '��', $location),
    // ����
    'property' => array(),
    // ������Ϣ
    'business' => array(
        'merit' => array('title' => '�ۺ�����', 'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score))), // �ۺ�����
        'totaltrade' => array('title' => '���״���', 'value' => $trade_num . '��'), // ���״���
        // 'totaltrade' => array('title' => '���״���', 'value' => $profile['review_times']), // ���״���
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220075&type=inner_app',
    ),
    'showtitle' => '�����б�',
    // ��Ʒչʾ
    'showcase' => array(),
    // ������Ʒ
    'morecase' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&pid=1220109&goods_type=all&type=inner_app',
    // ����
    'share' => array(),
    // ��ѯ
    'consult_link' => 'yueyue://goto?user_id=' . $user_id . '&receiver_id=' . $profile['user_id'] . '&receiver_name=' .
        urlencode(mb_convert_encoding($profile['name'], 'utf8', 'gbk')) . '&pid=1220021&type=inner_app',
);

// ��ȡ�û�������Ϣ
$share_result = $seller_obj->get_share_text($seller_user_id);
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
$goods_list = $task_goods_obj->user_goods_list($seller_user_id, $data, false, 'goods_id DESC', '0,6', '*');
if (version_compare($version, '3.3', '>')) {
    // ���Ϣ
    $data = array(
        'action_type' => 1,
    );
    $event_list = $task_goods_obj->user_goods_list($seller_user_id, $data, false, 'goods_id DESC', '0,6', '*');
    if ($location_id == 'test2') {
        $options['data'] = array(
            '$seller_user_id' => $seller_user_id,
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
if (count($goods_list) < 6 && version_compare($version, '3.1') >= 0) {
    // �Ƿ���ʾ �鿴���� ����
    unset($user_info['morecase']);
}
$promotion_obj = POCO::singleton('pai_promotion_class');  // ����
foreach ($goods_list as $value) {
    $type_id = $value['type_id'];  // �����
    $price_str = $value['prices'];
    $prices_list = unserialize($value['prices_list']);
    $pro_prices_list = array();
    if (!empty($prices_list)) {
        $tmp = 0;
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
    } else {
        $pro_prices_list = $price_str;  // �����۸�(for ����)
    }
    $goods_id = $value['goods_id']; // ��ƷID
    $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app'; // ��������
    if ($type_id == 42) {
        // �����
        $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&goods_id=' . $goods_id . '&pid=1220152&type=inner_app';
    }
    $case_info = array(
        'goods_id' => $goods_id,
        'title' => $value['titles'],
        'prices' => '��' . $price_str,
        'pic' => $value['images'],
        'link' => $link,   // ��������
//        'content' => $value['content'],
    );
    if (!empty($pro_prices_list) && version_compare($version, '3.2', '>') && $type_id != 42) {
        $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '��ʡ: ' . $promotion['abate'];
            // ��� ������Ϣ
            $case_info = array_merge($case_info, $promotion);
        }
    }
    if (version_compare($version, '3.3', '<')) {
        $user_info['showcase'][] = $case_info;
        continue;
    }
    $add_time = $value['add_time'];  // ���ʱ��
    $showcase[$add_time] = $case_info;
}
if (version_compare($version, '3.3', '>')) {
    krsort($showcase);
    $user_info['showcase'] = array_slice($showcase, 0, 6);
}

$options['data'] = $user_info;
return $cp->output($options);

