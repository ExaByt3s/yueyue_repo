<?php

/**
 * 搜索 商家列表页
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
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$type_id = $client_data['data']['param']['type_id'];  // 大分类ID
$keyword = $client_data['data']['param']['keyword'];   // 搜索关键词
$page = intval($client_data['data']['param']['page']);  // 第几页
$rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)
$limit = trim($client_data['data']['param']['limit']);  // 传值如: 0,20
$screen = $client_data['data']['param']['screen_query']; //筛选字段
$screen_show = $client_data['data']['param']['screen_show']; // 是否显示筛选条件

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
if (empty($keyword)) {  // 没有查询条件
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
$default_cover = $mall_seller_obj->_seller_cover;  // 默认背景
$seller_list = array();
foreach ($seller_result['data'] as $seller) {
    $seller_id = $seller['user_id'];
    $score = interface_reckon_average_score($seller['total_overall_score'], $seller['review_times']);
    $cover = empty($seller['cover']) ? $default_cover : $seller['cover'];
    $service_info = array(
        'user_id' => $seller_id, // 商家ID
        'name' => $seller['name'], // 商家名称
        'cover' => yueyue_resize_act_img_url($cover, '260'), // 背景图
        'type_id' => trim($seller['type_id'], ','), // 分类
        'score' => sprintf('%.1f', $score), // 评分
        'trade_num' => '交易次数：' . intval($seller['seller_bill_finish_num']) . '次',
        'goods_num' => '提供服务项目 ' . intval($seller['onsale_num']) . '个',
        'link' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // 商家首页
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
            'user_id' => $seller['seller_user_id'], // 商家ID
            'name' => $seller['seller'], // 商家名称
            'cover' => yueyue_resize_act_img_url($seller['images'], '260'), // 背景图
            'score' => sprintf('%.1f', $seller['step']), // 评分
            'trade_num' => $seller['titles'],
            'goods_num' => $seller['buy_num'],
            'link' => $seller['link'], // 商家首页
        );
    }
    $seller_result['total'] = count($seller_list);
}
$orderby = $filter = array();
if (version_compare($version, '3.2', '>') && $screen_show == 1) {
//    $screening = interface_get_search_screening($type_id,false);  // 筛选条件
//    $orderby = $screening['orderby'];
//    $filter = $screening['filter'];
}

$options['data'] = array(
    'recommend' => $recommend,
    'total' => intval($seller_result['total']),
    'list' => $seller_list,
    'filter' => array(), // 商家没有筛选条件
    'orderby' => $orderby,
);
return $cp->output($options);
