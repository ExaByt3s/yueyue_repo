<?php

/**
 * 搜索 商品列表页
 *
 * @since 2015-7-17
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$search_location = $client_data['data']['param']['search_location'];  // 查询位置
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$type_id = $client_data['data']['param']['type_id'];   // 大分类ID
$keyword = $client_data['data']['param']['keyword'];   // 搜索关键词
$page = intval($client_data['data']['param']['page']);  // 第几页
$rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)
$limit = trim($client_data['data']['param']['limit']);  // 传值如: 0,20
$screen = $client_data['data']['param']['screen_query']; //筛选字段
$return_query = $client_data['data']['param']['return_query']; //初始化筛选
$screen_show = $client_data['data']['param']['screen_show']; // 是否显示筛选条件

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
//$screening = interface_get_search_screening($type_id, true, array());  // 筛选条件
$tmp_filter = array();
foreach ($result as $attr) {
    $key = $attr['name'];
    $title = $attr['text'];  // 显示名称
    $choice_type = strval($attr['select_type']);  // 1单选,2多选
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
                // 活动[自定义], 不需要
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
    'detail[382]383' => 'detail[400]',  // 摄影培训
    'detail[382]385' => 'detail[402]',  // 美体培训
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


if (empty($keyword) && empty($return_query)) {  // 没有查询条件
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
    // 数字类型的 搜索, 不需要 类型判断
    $data = array(
        'keywords' => $keyword,
        'type_id' => intval($type_id),
    );
}
$r_data = array();
if (!empty($return_query)) {  // 初始化数据
    $en_ret = mb_convert_encoding(urldecode($return_query), 'gbk', 'utf-8');
    parse_str($en_ret, $r_data);   // 解析成数组
    if (isset($r_data['type_id'])) {
        $type_id = $r_data['type_id'];
    }
    $data = $r_data;
}
if ($user_id == 'test') {
    $data['debug'] = 1;
}
if (!empty($screen)) {  // 筛选数据
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
        // 支持 web 2015-11-9
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
$promotion_obj = POCO::singleton('pai_promotion_class');  // 促销
foreach ($goods_list['data'] as $goods) {
    $goods_id = $goods['goods_id'];
    $price_str = sprintf('%.2f', $goods['prices']);
    $prices_list = unserialize($goods['prices_list']);
    if ($type_id == 42) {
        $prices_list = unserialize($goods['goods_prices_list']);  // 价格列表
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
                'prices_type_id' => $k, //必填
                'goods_prices' => $show_price, //必填
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
            $unit = $price_unit . ' 起';
        }
    }
    if ($type_id == 42) {  // 活动
        $attend_num = intval($goods['stock_num_total']) - intval($goods['stock_num']);  // 参加人数
        $attend_num = $attend_num < 0 ? 0 : $attend_num;  // fix计算错误
        // time_s是场次的开始时间, time_e是场次的结束时间, f_time是活动最开始的时间, e_time是活动最后的结束时间
        $avatar = get_seller_user_icon($goods['user_id'], 86);
        $size = strpos($goods['images'], '.poco.cn') === false ? 260 : 640;
        $is_official = $goods['is_official'];  // 是否官方
        $seller_user_id = $goods['user_id'];
        $seller_name = empty($goods['seller_name']) ? get_seller_nickname_by_user_id($seller_user_id) : $goods['seller_name'];
        $is_finish = interface_activity_is_finish($goods['e_time'], 0, $goods['is_over']);  // 活动是否结束
        $start_time = empty($goods['f_time']) ? '未知' : date('Y-m-d', $goods['f_time']); // 活动开始时间
        $score = interface_reckon_average_score($goods['total_overall_score'], $goods['review_times']); // 评分
        $service_info = array(
            'goods_id' => $goods_id, // 商品ID
            'titles' => $goods['titles'], // 服务名称
            'type_id' => $goods['type_id'], // 服务分类
            'images' => yueyue_resize_act_img_url($goods['images'], $size), // 图片展示
            'prices' => '￥' . $price_str,
            'rate' => '￥' . $price_str,
            'unit' => $unit,
            'start_time' => '活动时间: ' . $start_time,
            'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220152&type=inner_app',
            'buy_num' => '已有' . $attend_num . '人参加',
            'seller_id' => $seller_user_id,
            'seller' => $seller_name,
            'seller_avatar' => $avatar,
            'is_show' => intval($goods['is_show']),  // 1 在售, 2 下架
            'is_finish' => $is_finish ? 1 : 0,  // 0 可参与 1 结束
            'sign' => $is_official == 1 ? '官方' : '',
            'sign_bg' => $is_official == 1 ? 1 : 0,
            'score' => $score, // 评分
        );
    } else {
        $service_info = array(
            'user_id' => $goods['user_id'],
            'goods_id' => $goods_id, // 商品ID
            'titles' => $goods['titles'], // 服务名称
            'type_id' => $goods['type_id'], // 服务分类
            'images' => yueyue_resize_act_img_url($goods['images'], 260), // 图片展示
            'prices' => '￥' . $price_str . $unit,
            'rate' => '￥' . $price_str,
            'unit' => $unit,
            'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app',
            'buy_num' => '已有' . intval($goods['bill_pay_num']) . '人购买', // + $goods['old_bill_finish_num']
//        // 添加 促销信息
//        'abate' => '立省: 100',
//        'notice' => '限时低价',
//        'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
        );
    }
    if (!empty($pro_prices_list) && version_compare($version, '3.2', '>')) {
        $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '立省: ' . $promotion['abate'];
            // 添加 促销信息
            $service_info = array_merge($service_info, $promotion);
        }
    }
    $service_list[] = $service_info;
}

$recommend = 0;
if (empty($service_list) && version_compare($version, '3.3', '>') && empty($screen) && empty($return_query)) {
    // 无结果, 并"非筛选"  则出 推荐
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
    $screening = interface_get_search_screening($type_id, true, $r_data);  // 筛选条件
    $orderby = $screening['orderby'];
    $filter = $screening['filter'];
}

$options['data'] = array(
    'recommend' => $recommend,  // 是否推荐
    'total' => $goods_list['total'],
    'list' => $service_list,
    'filter' => $filter,
    'orderby' => $orderby,
);
return $cp->output($options);
