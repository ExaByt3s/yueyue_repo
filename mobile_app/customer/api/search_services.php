<?php

/**
 * ���� ��Ʒ�б�ҳ
 *
 * @since 2015-7-17
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$search_location = $client_data['data']['param']['search_location'];  // ��ѯλ��
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$type_id = $client_data['data']['param']['type_id'];   // �����ID
$keyword = $client_data['data']['param']['keyword'];   // �����ؼ���
$page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
$rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
$screen = $client_data['data']['param']['screen_query']; //ɸѡ�ֶ�
$return_query = $client_data['data']['param']['return_query']; //��ʼ��ɸѡ
$screen_show = $client_data['data']['param']['screen_show']; // �Ƿ���ʾɸѡ����

if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $limit;
}

include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
$result = $attribute_obj->property_for_search_get_data($type_id, true);
//$screening = interface_get_search_screening($type_id, true, array());  // ɸѡ����
$tmp_filter = array();
foreach ($result as $attr) {
    $key = $attr['name'];
    $title = $attr['text'];  // ��ʾ����
    $choice_type = strval($attr['select_type']);  // 1��ѡ,2��ѡ
    $options = array();
    $sub_attr = $attr['data'];
    if (empty($sub_attr)) {
        $filter[] = array('key' => $key, 'title' => $title, 'options' => $options,);
        continue;
    }
    foreach ($sub_attr as $val) {
        $sub_title = $val['val'];
        $sub_value = $val['key'];
        if ($type_id == 42) {
            if ($key == 'front_time' && $sub_value == 'self') {
                // �[�Զ���], ����Ҫ
                continue;
            }
        }
        $sub_options = array();
        $sub_val = $val['child_data'];
        if (isset($sub_val['data']) && !empty($sub_val['data'])) {
            foreach ($sub_val['data'] as $v) {
                $sub_options[] = array(
                    'key' => $sub_val['name'], 'title' => $v['val'], 'value' => $v['key'],);
            }
        }
        $options[] = array(
            'key' => $key,
            'title' => $sub_title,
            'choice_type' => $choice_type,
            'value' => $sub_value,
            'options' => $sub_options,
        );
    }
    if (in_array($key, array('detail[400]', 'detail[402]'))) {
        $tmp_filter[$key] = array('key' => $key, 'title' => $title, 'choice_type' => $choice_type, 'options' => $options,);
        continue;
    }
    $filter[] = array('key' => $key, 'title' => $title, 'choice_type' => $choice_type, 'options' => $options,);
}
$tag_keymap = array(
    'detail[382]383' => 'detail[400]',  // ��Ӱ��ѵ
    'detail[382]385' => 'detail[402]',  // ������ѵ
);
foreach ($filter as $key => $type) {
    foreach ($type['options'] as $k => $item) {
        $map_key = $item['key'] . $item['value'];
        if (isset($tag_keymap[$map_key])) {
            $tags_key = $tag_keymap[$map_key];
            $filter[$key]['options'][$k]['options'][] = $tmp_filter[$tags_key];
        }
    }
}
$options['data'] = $filter;
return $cp->output($options);


if (empty($keyword) && empty($return_query)) {  // û�в�ѯ����
    $options['data'] = array(
        'total' => 0,
        'list' => array(),
        'msg' => 'keyword is empty!',
    );
    return $cp->output($options);
}
$search_location = empty($search_location) ? $location_id : $search_location;
$data = array(
    'location_id' => $search_location,
    'keywords' => $keyword,
);
if (!empty($type_id)) {
    $data['type_id'] = intval($type_id);
}
if (is_numeric($keyword)) {
    // �������͵� ����, ����Ҫ �����ж�
    $data = array(
        'keywords' => $keyword,
        'type_id' => intval($type_id),
    );
}
$r_data = array();
if (!empty($return_query)) {  // ��ʼ������
    $en_ret = mb_convert_encoding(urldecode($return_query), 'gbk', 'utf-8');
    parse_str($en_ret, $r_data);   // ����������
    if (isset($r_data['type_id'])) {
        $type_id = $r_data['type_id'];
    }
    $data = $r_data;
}
if ($user_id == 'test') {
    $data['debug'] = 1;
}
if (!empty($screen)) {  // ɸѡ����
    if (is_array($screen)) {
        $screen_str = '';
        foreach ($screen as $key => $val) {
            if (empty($val)) {
                continue;
            }
            $screen_str .= $key . '=' . $val . '&';
        }
        if (!empty($screen_str)) {
            parse_str(trim($screen_str, '&'), $r_data);
            $data = array_merge($data, $r_data);
        }
    } else {
        // ֧�� web 2015-11-9
        parse_str(trim(urldecode($screen)), $r_data);
        $data = array_merge($data, $r_data);
    }
}
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
if ($type_id == 42) {
    // for test
    $data['is_show'] = 1;
    $goods_list = $task_goods_obj->search_goods_list_by_fulltext($data, $limit_str);
} else {
    $goods_list = $task_goods_obj->user_search_goods_list($data, $limit_str);
}
if ($user_id == 'test1') { //for debug
    $options['data'] = array(
        '$data' => $data,
        '$limit_str' => $limit_str,
        '$goods_list' => $goods_list,
    );
    return $cp->output($options);
}
$service_list = array();
$promotion_obj = POCO::singleton('pai_promotion_class');  // ����
foreach ($goods_list['data'] as $goods) {
    $goods_id = $goods['goods_id'];
    $price_str = sprintf('%.2f', $goods['prices']);
    $prices_list = unserialize($goods['prices_list']);
    if ($type_id == 42) {
        $prices_list = unserialize($goods['goods_prices_list']);  // �۸��б�
    }
    $pro_prices_list = $price_str;
    $unit = '';
    if (!empty($prices_list)) {
        $tmp = 0;
        $price_unit = '';
        $pro_prices_list = array();
        foreach ($prices_list as $k => $price) {
            $show_price = $type_id == 42 ? $price['prices'] : $price;
            if ($show_price <= 0) {
                continue;
            }
            $pro_prices_list[] = array(
                'prices_type_id' => $k, //����
                'goods_prices' => $show_price, //����
            );
            if ($tmp > 0 && bccomp($show_price, $tmp, 2) > 0) {
                continue;
            }
            $tmp = $show_price;
            if ($type_id == 42) {
                $price_unit = $price['name'];
                $price_unit = empty($price_unit) ? '' : '/' . $price_unit;
                $unit = $price_unit;
            }
        }
        if ($tmp > 0 && count($prices_list) > 1) {
            $price_str = sprintf('%.2f', $tmp);
            $unit = $price_unit . ' ��';
        }
    }
    if ($type_id == 42) {  // �
        $attend_num = intval($goods['stock_num_total']) - intval($goods['stock_num']);  // �μ�����
        $attend_num = $attend_num < 0 ? 0 : $attend_num;  // fix�������
        // time_s�ǳ��εĿ�ʼʱ��, time_e�ǳ��εĽ���ʱ��, f_time�ǻ�ʼ��ʱ��, e_time�ǻ���Ľ���ʱ��
        $avatar = get_seller_user_icon($goods['user_id'], 86);
        $size = strpos($goods['images'], '.poco.cn') === false ? 260 : 640;
        $is_official = $goods['is_official'];  // �Ƿ�ٷ�
        $seller_user_id = $goods['user_id'];
        $seller_name = empty($goods['seller_name']) ? get_seller_nickname_by_user_id($seller_user_id) : $goods['seller_name'];
        $is_finish = interface_activity_is_finish($goods['e_time'], 0, $goods['is_over']);  // ��Ƿ����
        $start_time = empty($goods['f_time']) ? 'δ֪' : date('Y-m-d', $goods['f_time']); // ���ʼʱ��
        $score = interface_reckon_average_score($goods['total_overall_score'], $goods['review_times']); // ����
        $service_info = array(
            'goods_id' => $goods_id, // ��ƷID
            'titles' => $goods['titles'], // ��������
            'type_id' => $goods['type_id'], // �������
            'images' => yueyue_resize_act_img_url($goods['images'], $size), // ͼƬչʾ
            'prices' => '��' . $price_str,
            'rate' => '��' . $price_str,
            'unit' => $unit,
            'start_time' => '�ʱ��: ' . $start_time,
            'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220152&type=inner_app',
            'buy_num' => '����' . $attend_num . '�˲μ�',
            'seller_id' => $seller_user_id,
            'seller' => $seller_name,
            'seller_avatar' => $avatar,
            'is_show' => intval($goods['is_show']),  // 1 ����, 2 �¼�
            'is_finish' => $is_finish ? 1 : 0,  // 0 �ɲ��� 1 ����
            'sign' => $is_official == 1 ? '�ٷ�' : '',
            'sign_bg' => $is_official == 1 ? 1 : 0,
            'score' => $score, // ����
        );
    } else {
        $service_info = array(
            'user_id' => $goods['user_id'],
            'goods_id' => $goods_id, // ��ƷID
            'titles' => $goods['titles'], // ��������
            'type_id' => $goods['type_id'], // �������
            'images' => yueyue_resize_act_img_url($goods['images'], 260), // ͼƬչʾ
            'prices' => '��' . $price_str . $unit,
            'rate' => '��' . $price_str,
            'unit' => $unit,
            'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app',
            'buy_num' => '����' . intval($goods['bill_pay_num']) . '�˹���', // + $goods['old_bill_finish_num']
//        // ��� ������Ϣ
//        'abate' => '��ʡ: 100',
//        'notice' => '��ʱ�ͼ�',
//        'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
        );
    }
    if (!empty($pro_prices_list) && version_compare($version, '3.2', '>')) {
        $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '��ʡ: ' . $promotion['abate'];
            // ��� ������Ϣ
            $service_info = array_merge($service_info, $promotion);
        }
    }
    $service_list[] = $service_info;
}

$recommend = 0;
if (empty($service_list) && version_compare($version, '3.3', '>') && empty($screen) && empty($return_query)) {
    // �޽��, ��"��ɸѡ"  ��� �Ƽ�
    $recommend = 1;
    $mall_search_obj = POCO::singleton('pai_search_class');
    $service_list = $mall_search_obj->get_search_recommend_content('goods', $type_id, $search_location);
    if ($user_id == 'test2') { //for debug
        $options['data'] = array(
            '$type_id' => $type_id,
            '$search_location' => $search_location,
            '$service_list' => $service_list,
        );
        return $cp->output($options);
    }
    $goods_list['total'] = count($service_list);
}
$orderby = $filter = array();
if (version_compare($version, '3.2', '>') && $screen_show == 1) {
    define('G_MALL_PROJECT_USER_ROOT', "http://www.yueus.com/mall/user/test");
    $screening = interface_get_search_screening($type_id, true, $r_data);  // ɸѡ����
    $orderby = $screening['orderby'];
    $filter = $screening['filter'];
}

$options['data'] = array(
    'recommend' => $recommend,  // �Ƿ��Ƽ�
    'total' => $goods_list['total'],
    'list' => $service_list,
    'filter' => $filter,
    'orderby' => $orderby,
);
return $cp->output($options);
