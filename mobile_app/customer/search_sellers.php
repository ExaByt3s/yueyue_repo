<?php

/**
 * ���� �̼��б�ҳ
 *
 * @since 2015-7-17
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$version = $client_data['data']['version'];
$location_id = $client_data['data']['param']['location_id'];
$search_location = $client_data['data']['param']['search_location'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$type_id = $client_data['data']['param']['type_id'];  // �����ID
$keyword = $client_data['data']['param']['keyword'];   // �����ؼ���
$page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
$rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
$screen = $client_data['data']['param']['screen_query']; //ɸѡ�ֶ�
$screen_show = $client_data['data']['param']['screen_show']; // �Ƿ���ʾɸѡ����

$tongji_type = '/mobile_app/customer/search_sellers';
$tongji_query = 'query=' . serialize($client_data['data']['param']);
yueyuetj_touch_log($tongji_type, $tongji_query);


if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}
if (empty($keyword)) {  // û�в�ѯ����
    $options['data'] = array(
        'total' => 0,
        'list' => array(),
    );
    return $cp->output($options);
}
$search_location = empty($search_location) ? $location_id : $search_location;
if (!is_numeric($keyword)) {
    $data = array(
        'type_id' => intval($type_id),
        'location_id' => $search_location,
        'keywords' => $keyword,
    );
} else {
    $data = array(
        'keywords' => $keyword,
    );
}
if ($screen) {
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
}

$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$seller_result = $mall_seller_obj->user_search_seller_list($data, $limit_str);
if ($user_id == 'test') { //for debug
    $options['data'] = array(
        '$data' => $data,
        '$limit_str' => $limit_str,
        '$seller_result' => $seller_result,
    );
    return $cp->output($options);
}
$default_cover = $mall_seller_obj->_seller_cover;  // Ĭ�ϱ���
$seller_list = array();
foreach ($seller_result['data'] as $seller) {
    $seller_id = $seller['user_id'];
    $score = interface_reckon_average_score($seller['total_overall_score'], $seller['review_times']);
    $cover = empty($seller['cover']) ? $default_cover : $seller['cover'];
    $service_info = array(
        'user_id' => $seller_id, // �̼�ID
        'name' => $seller['name'], // �̼�����
        'cover' => yueyue_resize_act_img_url($cover, '260'), // ����ͼ
        'type_id' => trim($seller['type_id'], ','), // ����
        'score' => sprintf('%.1f', $score), // ����
        'trade_num' => '���״�����' . intval($seller['seller_bill_finish_num']) . '��',
        'goods_num' => '�ṩ������Ŀ ' . intval($seller['onsale_num']) . '��',
        'link' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // �̼���ҳ
    );
    $seller_list[] = $service_info;
}

$recommend = 0;
if (empty($seller_list) && version_compare($version, '3.3', '>')) {
    $recommend = 1;
    $mall_search_obj = POCO::singleton('pai_search_class');
    $seller_result = $mall_search_obj->get_search_recommend_content('seller', $type_id, $search_location);
    foreach ($seller_result as $seller) {
        $seller_list[] = array(
            'user_id' => $seller['seller_user_id'], // �̼�ID
            'name' => $seller['seller'], // �̼�����
            'cover' => yueyue_resize_act_img_url($seller['images'], '260'), // ����ͼ
            'score' => sprintf('%.1f', $seller['step']), // ����
            'trade_num' => $seller['titles'],
            'goods_num' => $seller['buy_num'],
            'link' => $seller['link'], // �̼���ҳ
        );
    }
    $seller_result['total'] = count($seller_list);
}
$orderby = $filter = array();
if (version_compare($version, '3.2', '>') && $screen_show == 1) {
//    $screening = interface_get_search_screening($type_id,false);  // ɸѡ����
//    $orderby = $screening['orderby'];
//    $filter = $screening['filter'];
}

$options['data'] = array(
    'recommend' => $recommend,
    'total' => intval($seller_result['total']),
    'list' => $seller_list,
    'filter' => array(), // �̼�û��ɸѡ����
    'orderby' => $orderby,
);
return $cp->output($options);
