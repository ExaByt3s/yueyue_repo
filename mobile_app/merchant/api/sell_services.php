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

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->user_get_goods_info($goods_id, $user_id);
if ($location_id == 'test') { //for debug
    $options['data'] = $goods_result;
    return $cp->output($options);
}
if ($goods_result['result'] != 1) {
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
$location = get_poco_location_name_by_location_id($user_result['seller_data']['location_id']);
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
        if ($max <= 0 || bccomp($prices, $max, 2) > 0) { // ���۸�
            $max = sprintf('%.2f', $prices);
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
    $max = sprintf('%.2f', $max);
    $goods_prices = $min . '-' . $max;
    $prices_unit = '';
    $show_prices = $min . '-' . $max;
} else {
    $show_prices = $goods_prices;
    $prices_unit = ($type_id == 42) ? '' : 'Ԫ';
}
//$score = $goods['goods_data']['average_score']; // �ۺ�����
$score = interface_reckon_average_score($goods['goods_data']['total_overall_score'], $goods['goods_data']['review_times']);
// ��Ʒ����
$contents = trim($goods['default_data']['content']['value']);
$contents = interface_content_replace_pics($contents, 640);  // �滻ͼƬ��С
if (!empty($contents)) {
    $contents = interface_content_to_ubb($contents);
}
$is_show = intval($goods['goods_data']['is_show']);  // ���¼����
$type_name = $task_goods_obj->get_goods_typename_for_type_id($type_id);  // ��ȡ��������
$type_name = empty($type_name) ? '' : '[' . $type_name . '] ';

// ���˽���
$introduce = array();
if ($type_id == 42) {
    $introduce = array(
        'title' => '��֯�߼��',
        'value' => interface_content_strip($user_result['seller_data']['introduce']),
    );
}
$titles = preg_replace('/&#\d+;/', '', $goods['goods_data']['titles']);  // ����
$bill_pay_num = $goods['goods_data']['statistical']['bill_pay_num'];  // ���״���
$goods_info = array(
    'goods_id' => $goods['goods_data']['goods_id'],
    'is_show' => $is_show, // �Ƿ�����: 1 ���� 2 �¼�
    'show_str' => $is_show == 2 ? '���¼�' : '����',
    // ���� �ֲ�ͼƬ
    'preview' => $preview,
    'zoom' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250026&type=inner_app',
    'title' => $type_name . $titles,
    'prices' => '��' . $show_prices, // �۸�or�۸�����
    'rate' => '��' . $goods_prices,
    'unit' => $prices_unit,
    'original_prices' => '', // ԭʼ�۸�
    'favor' => array(),
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
            'request' => 'yueseller://goto?user_id=' . $goods_user_id . '&pid=1250004&type=inner_app',
        ),
    ),
    'business' => array(
        'merit' => array('title' => '��������', 'value' => strval($score)), // �ۺ�����
        // ���״���
        'totaltrade' => array(
            'title' => '���״���',
            // $goods['goods_data']['review_times'] ���۴���
            'value' => $goods['goods_data']['statistical']['bill_pay_num'],
            // ��Ʒ����
            'request' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods['goods_data']['goods_id'] . '&pid=1250009&type=inner_app',
        ),
        // for ����android�汾(�°汾����)
        'request' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods['goods_data']['goods_id'] . '&pid=1250009&type=inner_app',
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
);

// 1.1 �汾֧�ֵ� ����༭����
if (($type_id == 31 && version_compare($version, '1.1', '>')) ||
    (!in_array($type_id, array(42, 43)) && version_compare($version, '1.2', '>'))
) {
    $status = $goods['goods_data']['status']; // ״̬ 0 δ��� 1 ͨ�� 2δͨ�� 3ɾ��
    $status_arr = array(
        0 => 'review',
        1 => 'pass',
        2 => 'rejected',
        3 => 'remove',
    );
    $goods_info['status_str'] = $status_arr[$status];  // ����״̬
    // ��ӱ༭����
    $goods_info['edit_url'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&operate=edit&pid=1250028&type=inner_app';
    // �ܾ�ԭ��
    $task_log_obj = POCO::singleton('pai_task_admin_log_class');
    $notice = '';
    if ($status == 2) {
        $reject_data = array('type_id' => 2007, 'action_type' => 2, 'action_id' => $goods_id);
        $log_result = $task_log_obj->get_log_by_type_last($reject_data);  // �ܾ�ԭ��
        $notice = trim(preg_replace('/\d+:/', '', $log_result['note']));
    }
    $goods_info['notice'] = $notice;
}

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
    $showing_exhibit = array();
    foreach ($exhibit['showing_exhibit'] as $showing) {
        // ��������
        $new_order_url = $order_url . '&stage_id=' . $showing['stage_id'];
        $showing['request'] = 'yueyue://goto?type=inner_web&url=' . urlencode($new_order_url) . '&wifi_url=' . urlencode($new_order_url) . '&showtitle=1';
        $showing_exhibit[] = $showing;
    }
    $goods_info['showing'] = array(
        'title' => '�����',
        'more_title' => '�鿴���ೡ��',
        'is_finish' => $exhibit['is_finish'],  // 0������ ��1 �ѽ���
        'attend' => $exhibit['all_exhibit'][0],
        'exhibit' => $showing_exhibit,
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
        $exhibit['request'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id .
            '&stage_id=' . $exhibit['stage_id'] . '&pid=1250045&type=inner_app'; // ��Ʒ����
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
//        'request' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=&type=inner_app', // ��Ʒ����
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
// 1.2.0 ����  2015-10-12
if (version_compare($version, '1.2', '>=')) {
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
