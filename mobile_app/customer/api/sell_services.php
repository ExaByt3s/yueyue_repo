<?php

/**
 * ������Ʒҳ
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = $client_data['data']['param']['goods_id'];   // ��Ʒ���
list($goods_id, $direct_order_id) = explode('_', $goods_id);   // ���� ɨ�������µ�
$request_platform = $client_data['data']['param']['request_platform'];   // ����ƽ̨ ( for ����web 2015-7-29 )

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->get_goods_info_by_goods_id($goods_id);
if ($location_id == 'test') { //for debug
    $options['data'] = $goods_result;
    return $cp->output($options);
}
// ��� ͳ�� 2015-9-22
$mall_statistical_obj = POCO::singleton('pai_mall_statistical_class');
$mall_statistical_obj->user_update_goods_view($goods_id);

if ($goods_result['result'] != 1 && $goods_result['result'] != -3) {
    return $cp->output(array('data' => array()));
}
$goods = $goods_result['data'];
if (empty($goods)) {
    return $cp->output(array('data' => array()));
}
// ��ȡ��Ʒ����
$type_id = intval($goods['goods_data']['type_id']);  // ��Ʒ����
$status = intval($goods['goods_data']['status']);  // ״̬
$goods_property = interface_get_goods_property($goods['system_data'], $goods['goods_data']['location_id']);
$service = $goods_property['service'];
$guide = $goods_property['guide'];
$menu = $goods_property['menu'];;  // ��������
$recommend = $goods_property['recommend']; // �Ƽ�
$guide_img = $goods_property['guide_img'];  // ����
$package = $goods_property['package'];  // �ײ�
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
$type_id_arr = explode(',', $profile['type_id']);  // �û���֤
$user_name = $profile['name'];  // �̼�����
$user_avatar = $profile['avatar']; // get_user_icon($goods_user_id, 86, TRUE); // $profile['avatar'];  // ͷ��
$location = get_poco_location_name_by_location_id($profile['location_id']);
$standard = array();  // ���
$goods_prices = sprintf('%.2f', $goods['default_data']['prices']['value']); // �۸�
$pro_prices_list = $goods_prices;
$prices_data_list = $goods['prices_data_list'];  // �۸��б�
$package_keymap_ = array(  // �۸�/����ͼ��
    310 => 314,
    311 => 315,
    312 => 316
);
if (!empty($prices_data_list)) {
    $pro_prices_list = array();
    foreach ($prices_data_list as $value) {
        $prices = floatval($value['value']);
        if ($prices <= 0) {
            continue;
        }
        $standard_id = $value['id']; // �۸�ID
        $standard_name = $value['name'];  // �۸�����
        $unit = ($type_id == 42) ? '' : $value['name_val'];  // ��λ
        if ($type_id == 40) {  // ��Ӱ����
            $package_id = isset($package_keymap_[$standard_id]) ? $package_keymap_[$standard_id] : 0;
            if (isset($package[$package_id])) {
                $standard_name = $value['name_type'] . ':' . $package[$package_id];
                $unit = $package[$standard_id];
            } else {
                $unit = '';
            }
        }
        $unit = empty($unit) ? '' : $unit; // ��λ
        if ($min <= 0 || bccomp($prices, $min, 2) < 0) { // ��С�۸�
            $min = $prices; // sprintf('%.2f', $prices);
            $prices_unit = $unit; // ��λ
        }
        // for ����
        $pro_prices_list[] = array(
            'prices_type_id' => $value['id'], //����
            'goods_prices' => sprintf('%.2f', $prices), //����
            'prices_unit' => $unit,
        );
        if ($type_id == 42) {  // ��۸�
            $prices_list = $value['prices_list_data'];
            if (!empty($prices_list)) {
                $stage_id = $value['type_id'];  // ����ID
                $show_standard = array();
                foreach ($prices_list as $v) {
                    $show_standard[] = array(
                        'stage_id' => $stage_id,
                        'id' => $v['id'],
                        'name' => $v['name'],
                        'value' => sprintf('%.2f', $v['prices']),
                        'original' => '',
                        'unit' => '',  // ��λ
                        'num' => intval($v['stock_num']),  // ���  stock_num_total
                    );
                }
                $standard[] = $show_standard;
                continue;
            }
        }
        $standard[] = array(
            'id' => $standard_id,
            'name' => $standard_name,
            'value' => sprintf('%.2f', $prices),
            'original' => '',
            'unit' => $unit,  // ��λ
            'num' => $value['num']
        );
    }
}
// �۸�Χ
if (count($pro_prices_list) > 1) {
    $min = sprintf('%.2f', $min);
    $goods_prices = $min;
    $prices_unit = empty($prices_unit) ? ' ��' : '/' . $prices_unit . ' ��';
    $show_prices = $min . $prices_unit;
} else {
    $show_prices = $goods_prices;
    $prices_unit = ($type_id == 42) ? '' : 'Ԫ';
}
$score = $goods['goods_data']['average_score']; // �ۺ�����
$score = intval($score) <= 0 ? 5 : $score;
// ��Ʒ����
$contents = trim($goods['default_data']['content']['value']);
$contents = interface_content_replace_pics($contents, 640);  // �滻ͼƬ��С
if (!empty($contents) && $request_platform != 'web') {
    $contents = interface_content_to_ubb($contents);
}
// �µ�URL
$order_url = 'http://yp.yueus.com/mall/user/order/confirm.php?goods_id=' . $goods_id . '&type_id=' . $goods['goods_data']['type_id'] . '&direct_order_id=' . $direct_order_id;
if ($type_id == 42) {
    // for test (�)
    $order_url = 'http://yp.yueus.com/mall/user/test/order/confirm.php?goods_id=' . $goods_id . '&type_id=' . $goods['goods_data']['type_id'] . '&direct_order_id=' . $direct_order_id;
}
$is_show = intval($goods['goods_data']['is_show']);  // ���¼����
$type_name = $task_goods_obj->get_goods_typename_for_type_id($type_id);  // ��ȡ��������
$type_name = empty($type_name) ? '' : '[' . $type_name . '] ';
$favor = array(
    'title' => version_compare($version, '3.3', '>') ? '�ղ�' : '�ղظ÷���',
    'value' => '0', // 1 ���ղ�
);
if (version_compare($version, '3.2', '>')) {  // 3.2.0 �����ղ�
    $follow_user_obj = POCO::singleton('pai_mall_follow_user_class');
    $favor_res = $follow_user_obj->check_goods_follow($user_id, $goods_id);
    if (true == $favor_res) {
        $favor = array(
            'title' => '���ղ�',
            'value' => '1', // 1 ���ղ�
        );
    }
}
// ���˽���
$introduce = array();
if ($type_id == 42) {
    $introduce = array(
        'title' => '��֯�߼��',
        'value' => interface_content_strip($profile['introduce']),
    );
}
$titles = preg_replace('/&#\d+;/', '', $goods['goods_data']['titles']);  // ����
$bill_pay_num = $goods['goods_data']['statistical']['bill_pay_num'];  // ���״���
$goods_info = array(
    'goods_id' => $goods['goods_data']['goods_id'],
    'is_show' => $is_show, // �Ƿ�����: 1 ���� 2 �¼�
    'show_str' => $is_show == 2 ? '���¼�' : '�����µ�',
    // ���� �ֲ�ͼƬ
    'preview' => $preview,
    'zoom' => 'yueyue://goto?user_id=' . $user_id . '&pid=1250026&type=inner_app', // ����Ŵ�
    'title' => $type_name . $titles,
    'prices' => '��' . $show_prices, // �۸�or�۸�����
    'rate' => '��' . $goods_prices,
    'unit' => $prices_unit,
    'original_prices' => '', // ԭʼ�۸�
    'favor' => $favor,
//    'standard' => $standard, // ���
    'promise_title' => $type_id == 42 ? '�����' : '��������',
    'promise_more_title' => '��������',
    'promise' => $service, // ��������
    'user' => array(
        'title' => ($type_id == 42) ? '��֯����Ϣ' : '�̼�����',
        'user_id' => $goods_user_id,
        'name' => $user_name,
        'avatar' => $user_avatar,
        'location' => str_replace(' ', '��', $location),  // �м��õ�
        'introduce' => $introduce,   // ���˼��
        'homepage' => array(
            'title' => '�̼���ҳ',
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_user_id . '&pid=1220103&type=inner_app',
        ),
    ),
    'business' => array(
        'merit' => array('title' => '��������', 'value' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score))), // �ۺ�����
        'totaltrade' => array('title' => '���״���', 'value' => strval($bill_pay_num)), // ���״���
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_user_id . '&goods_id=' . $goods['goods_data']['goods_id'] . '&pid=1220075&type=inner_app', // ��Ʒ����
    ),
    'profile_type' => $type_id, // ��������
    // �û�����
    'property' => array(),
    // ͼ������
    'detail' => array(
        'title' => $type_id == 42 ? '�����' : $goods['default_data']['content']['name'],
        'value' => $contents,
    ),
    // ����
    'share' => $task_goods_obj->get_share_text($goods_id),
    // ��ť
    // android ���� 2015-7-8
    'order_link' => 'yueyue://goto?type=inner_web&url=' . urlencode($order_url) . '&wifi_url=' . urlencode($order_url) . '&showtitle=1',
    'consult_link' => '',  // ��ѯ����
);
// ��ѯ����
$service_url = 'yueyue://goto?goods_id=' . $goods_id . '&pid=' . ($type_id == 42 ? 1220152 : 1220102) . '&type=inner_app'; // �
$goods_info['consult_link'] = 'yueyue://goto?user_id=' . $user_id . '&receiver_id=' . $goods_user_id .
    '&receiver_name=' . urlencode(mb_convert_encoding($user_name, 'utf8', 'gbk')) . '&goods_id=' . $goods_id .
    '&pid=1220021&type=inner_app&media_type=goods&goods_name=' . urlencode(mb_convert_encoding($titles, 'utf8', 'gbk')) .
    '&price=��' . $goods['goods_data']['prices'] . '&buy_num=' . urlencode(mb_convert_encoding('����' . $bill_pay_num . '�˹���', 'utf8', 'gbk')) .
    '&image=' . urlencode(yueyue_resize_act_img_url($goods['goods_data']['images'], 260)) .
    '&url=' . urlencode($service_url);

$att_data = $profile['att_data'];
$seller_property = interface_get_seller_property($profile['att_data']);  // ��ȡ�û�����
foreach ($seller_property as $v) {
    $p_type_id = $v['type_id'];
    if ($p_type_id == $type_id) {
        $goods_info ['property'] = array($v);
        break;
    }
}
// Լ�
if ($type_id == 42) {
    // ��γ��
    $lng_lat = $goods['goods_data']['lng_lat'];
    list($lng, $lat) = explode(',', $lng_lat);
    $goods_info['grid'] = array(
        'title' => '��γ��(lng����,latγ��)',
        'lng' => floatval($lng),
        'lat' => floatval($lat),
    );
    // ���ʶ
    $is_official = intval($goods['goods_data']['is_official']);
    $goods_info['mark'] = array(
        'sign' => $is_official == 1 ? '�ٷ�' : '',
        'sign_bg' => $is_official == 1 ? 1 : 0,
    );
    // ����
    $contact_data = $goods['contact_data'];
    $contact_str = '';
    foreach ($contact_data as $contact) {
        $data = $contact['data'];
        $contact_str .= $data['name'] . ' ' . $data['phone'] . "\n";
    }
    if (!empty($contact_str)) {
        $goods_info['promise'][] = array(
            'id' => 'contact_data',
            'title' => '���ӣ�',
            'value' => trim($contact_str),
        );
    }
    // �鿴����
    $exhibit = interface_get_goods_showing($prices_data_list);
    $goods_info['showing'] = array(
        'title' => '�����',
        'more_title' => '�鿴���ೡ��',
        'is_finish' => $exhibit['is_finish'],  // 0������ ��1 �ѽ���
        'attend' => $exhibit['all_exhibit'][0],
        'exhibit' => $exhibit['showing_exhibit'],
    );
    if (empty($exhibit['showing_exhibit'])) {  // ȫ������
        $goods_info['showing']['is_finish'] = 1;
        $goods_info['showing']['closure'] = '�û�ѽ���';
        $goods_info['showing']['attend'] = end($exhibit['all_exhibit']);
        $goods_info['show_str'] = $is_show == 2 ? '���¼�' : '�ѽ���';  // �
    } elseif ($exhibit['is_finish'] == 1) { // ȫ�������ѱ���
        $goods_info['show_str'] = $is_show == 2 ? '���¼�' : '�ѽ���';  // �
    }
    // ��������
    $roster_list = array();
    foreach ($exhibit['all_exhibit'] as $exhibit) {
        $exhibit['request'] = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id .
            '&stage_id=' . $exhibit['stage_id'] . '&pid=1220154&type=inner_app'; // ��Ʒ����
        $roster_list[] = $exhibit;
    }
    $goods_info['roster'] = array(
        'title' => '��������',
        'value' => $roster_list,
    );
    // ��ع�
    $activity_review = $task_goods_obj->get_activity_review($goods_id);
    if ($location_id == 'test2') {
        $options['data'] = $activity_review; //
        return $cp->output($options);
    }
    $review_content = $activity_review['content'];
    if ($request_platform != 'web') {
        $review_content = interface_content_to_ubb($review_content);
    }
    $goods_info['review'] = array(
        'title' => '��ع�',
        'value' => $review_content,
//        'request' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=&type=inner_app', // ��Ʒ����
    );
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
// for web
if ($request_platform == 'web') {
    $goods_info['standard'] = $standard;
}
// 3.2.0 ����  2015-10-12
if (version_compare($version, '3.2', '>=')) {
    // �����б�
    $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, null, true);
    $promotion_list = array();
    if (!empty($promotion)) {
        $promotion['title'] = '�����';
        $promotion['abate'] = '��߿�ʡ����' . sprintf('%.2f', $promotion['abate']);
        $promotion['notice'] .= '��';
        $promotion_list = $promotion['promotion_list'];
        unset($promotion['promotion_list']);
    }
    $goods_info['promotion'] = $promotion;
    // �۸��б�
    $prices_list = array();
    foreach ($pro_prices_list as $prices) {
        $prices_type_id = $prices['prices_type_id'];
        $prices_promotion = isset($promotion_list[$prices_type_id]) ? $promotion_list[$prices_type_id] : array();
        $prices_list[] = array_merge(array(
            'id' => $prices_type_id,
            'prices' => '��' . $prices['goods_prices'],
            'unit' => empty($prices['prices_unit']) ? '' : '/' . $prices['prices_unit'],
        ), $prices_promotion);
    }
    $goods_info['prices_list'] = $prices_list;
}

$options['data'] = $goods_info; //
return $cp->output($options);
