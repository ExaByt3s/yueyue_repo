<?php

/**
 * 商品 列表
 *
 * @author heyaohua
 * @editor chenweibiao<chenwb@yueus.com>
 * @since 2015-6-23
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$classify_id = $client_data['data']['param']['classify_id'];
$return_query = $client_data['data']['param']['return_query'];   // 查询条件
$pid = $client_data['data']['param']['page_id'];   // PID
$title = $client_data['data']['param']['title'];   // 标题
$type_id = $client_data['data']['param']['type_id'];  // 大分类ID
$sorting = $client_data['data']['param']['sorting'];  // 排序
$limit = trim($client_data['data']['param']['limit']);  // 传值如: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // 第几页
    $rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}
$data = array();
if (!empty($return_query)) {
    $en_ret = mb_convert_encoding(urldecode($return_query), 'gbk', 'utf-8');
    parse_str($en_ret, $data);   // 解析成数组
} else {
    // 空参数
    $goods_list = array(
        'title' => empty($title) ? '服务列表' : $title,
        'goods' => array(),
        'total' => 0,
        'tt_link' => 'yueyue://goto?type=inner_app&pid=1220080', // TT私人定制
    );
    $options['data'] = $goods_list;
    return $cp->output($options);
}
$type_id = isset($data['type_id']) ? $data['type_id'] : $type_id;
$title = empty($title) ? ($type_id == 42 ? '活动列表' : '服务列表') : $title;
$goods = array();
$total = 0; // 总数
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$default_cover = $mall_seller_obj->_seller_cover;  // 默认背景
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
if ($data['yueyue_static_cms_id']) {
    // 获取榜单数据
    $cms_obj = new cms_system_class();
    $record_list = $cms_obj->get_last_issue_record_list(false, $limit_str, 'place_number DESC', $data['yueyue_static_cms_id']);
    // 计算总数
    $total = $cms_obj->get_last_issue_record_list(TRUE, $limit_str, 'place_number DESC', $data['yueyue_static_cms_id']);
    foreach ($record_list as $value) {
        if (empty($value['link_url']) && $data['cms_type'] == 'mall') {
            // 搜索商家
            $search_data = array(
//                'user_id' => $value['user_id'],
                'keywords' => $value['user_id'],
            );
            $result_list = $mall_seller_obj->user_search_seller_list($search_data, '0,1');
            if ($location_id == 'test') {
                $options['data'] = array(
                    '$search_data' => $search_data,
                    '$result_list' => $result_list,
                    'param' => $client_data['data']['param'],
                );
                return $cp->output($options);
            }
            $search_result = $result_list['data'][0];
            if (empty($search_result)) {
                continue;
            }
            $seller_id = $search_result['user_id'];
            $name = get_seller_nickname_by_user_id($seller_id);
            $buy_num = $search_result['bill_pay_num']; // 购买人数
            $cover = empty($search_result['cover']) ? $default_cover : $search_result['cover'];
            $goods[] = array(
                'goods_id' => 0,
                'seller_user_id' => $seller_id,
                'seller' => '', // fix double title 2015-9-6
                'titles' => $buy_num > 0 ? '已有' . $buy_num . '人购买' : '提供服务项目' . $search_result['onsale_num'] . '个', // preg_replace('/&#\d+;/', '', $search_result['seller_introduce']),
                'images' => yueyue_resize_act_img_url($cover, '640'),
                'link' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // 商家首页
                'prices' => empty($name) ? '商家' : $name, // 暂时作为商家名称
                'buy_num' => '提供服务项目' . $search_result['onsale_num'] . '个',
            );
            continue;
        }
        if (empty($value['link_url']) && $data['cms_type'] == 'goods') {
            // 搜索商品
            $r_data = array(
                'keywords' => $value['user_id'],
            );
        } else {
            $r_data = array();
            parse_str(urldecode($value['link_url']), $r_data);   // 解析成数组
        }
        $tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
        $goods_info = $tmp_result['data'][0];
        if (empty($goods_info) || $goods_info['is_show'] == 2) {
            $err_arr[] = array('$r_data' => $r_data, '$tmp_result' => $tmp_result);  // for debug;
            continue;
        }
        $goods_info_arr[] = array('$r_data' => $r_data, '$goods_info' => $goods_info);  // for debug
        $goods_id = $goods_info['goods_id'];
        $name = get_seller_nickname_by_user_id($goods_info['user_id']);
        $price_str = sprintf('%.2f', $goods_info['prices']);
        $prices_list = unserialize($goods_info['prices_list']);
        $pro_prices_list = $goods_info['prices'];  // for 促销
        if (!empty($prices_list)) {
            $min = 0;
            $pro_prices_list = array(); // for 促销
            foreach ($prices_list as $k => $v) {
                $v = intval($v);
                if ($v <= 0) {
                    continue;
                }
                $pro_prices_list[] = array(
                    'prices_type_id' => $k, //必填
                    'goods_prices' => $v, //必填
                );
                $min = ($min > 0 && bccomp($min, $v, 2) < 0) ? $min : $v;
            }
            if ($min > 0) {
                $price_str = sprintf('%.2f', $min) . '元 起';
            }
        }
        $buy_num = $goods_info['bill_pay_num'];
        $cover = empty($goods_info['images']) ? $default_cover : $goods_info['images'];
        $goods[] = array(
            'goods_id' => $goods_id,
            'seller' => empty($name) ? '商家' : $name,
            'titles' => preg_replace('/&#\d+;/', '', $goods_info['titles']),
            'images' => yueyue_resize_act_img_url($cover, '640'),
            'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app',
            'prices' => '￥' . $price_str,
//            'buy_num' => '已有' . $goods_info['bill_pay_num'] . '人购买',
            'buy_num' => $buy_num > 0 ? '已有' . $buy_num . '人购买' : $name,
            'pro_prices_list' => $pro_prices_list, // for 促销
        );
    }
    if ($location_id == 'test') {
        $options['data'] = array(
            '$err_arr' => $err_arr,
            '$goods_info_arr' => $goods_info_arr,
            '$record_list' => $record_list,
            'param' => $client_data['data']['param'],
        );
        return $cp->output($options);
    }
} else if (isset($data['s_action']) && $data['s_action'] == 'seller') {
    // 检索 商家列表 ( 全文搜索 )
    // type_id%3D31%26city%3D101029%26location_id%3D101029001%26total_times_s%3D1%26total_times_e%3D10%26total_money_s%3D1%26total_money_e%3D10%26status%3D1%26m_height%3D160%2C170%26s_action%3Dseller
    $search_result = $mall_seller_obj->user_search_seller_list($data, $limit_str);
    if ($location_id == 'test') {
        $options['data'] = array(
            '$data' => $data,
            '$search_result' => $search_result,
        );
        return $cp->output($options);
    }
    $total = $search_result['total'];
    // 组装数据返回
    $goods = array();
    foreach ($search_result['data'] as $value) {
        $seller_id = $value['user_id'];
        $name = get_seller_nickname_by_user_id($seller_id);
//        $price_str = sprintf('%.2f', $value['prices']);
        $buy_num = $value['bill_pay_num'];
        $cover = empty($value['cover']) ? $default_cover : $value['cover'];
        $goods[] = array(
            'goods_id' => 0,
            'seller_user_id' => $seller_id,
            'seller' => empty($name) ? '商家' : $name,
            'titles' => '', // preg_replace('/&#\d+;/', '', $value['seller_introduce']),
            'images' => yueyue_resize_act_img_url($cover, '640'),
            'link' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // 商家首页
            'prices' => empty($name) ? '商家' : $name, // 暂时作为商家名称
            'buy_num' => $buy_num > 0 ? '已有' . $buy_num . '人购买' : '提供服务项目' . $value['onsale_num'] . '个',
        );
    }
} else {
    // 检索 商品 ( 全文搜索 )
    if ($type_id == 42) {
        // for test
        $data['is_show'] = 1;
        $search_result = $task_goods_obj->search_goods_list_by_fulltext($data, $limit_str);
    } else {
        $search_result = $task_goods_obj->user_search_goods_list($data, $limit_str);
    }
//    $search_result = $task_goods_obj->user_search_goods_list($data, $limit_str);
    if ($location_id == 'test') {
        $options['data'] = array(
            '$data' => $data,
            '$client_data' => $client_data['data'],
            'urldecode($return_query)' => urldecode($return_query),
            '$search_result' => $search_result,
        );
        return $cp->output($options);
    }
    $total = $search_result['total'];
    $goods = array();
    foreach ($search_result['data'] as $value) {
        $goods_id = $value['goods_id'];
        $price_str = sprintf('%.2f', $value['prices']);
        $prices_list = unserialize($value['prices_list']);
        if ($type_id == 42) {
            $prices_list = unserialize($value['goods_prices_list']);  // 价格列表
        }
        $pro_prices_list = $price_str;
        $unit = '';
        if (!empty($prices_list)) {
            $tmp = 0;
            $price_unit = '';
            $pro_prices_list = array();  // for 促销
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
        $name = empty($value['seller_name']) ? get_seller_nickname_by_user_id($value['user_id']) : $value['seller_name'];
        $buy_num = $value['bill_pay_num'];
        $cover = empty($value['images']) ? $default_cover : $value['images'];
        $goods_info = array(
            'goods_id' => $goods_id,
            'seller' => empty($name) ? '商家' : $name,
            'titles' => preg_replace('/&#\d+;/', '', $value['titles']),
            'images' => $type_id == 42 ? yueyue_resize_act_img_url($cover, 145) : yueyue_resize_act_img_url($cover, 260),
            'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app',
            'prices' => '￥' . $price_str . $unit,
            'buy_num' => $buy_num > 0 ? '已有' . $buy_num . '人购买' : $name,
            'pro_prices_list' => $pro_prices_list,  // for 促销
        );
        if ($type_id == 42) {
            $attend_num = intval($value['stock_num_total']) - intval($value['stock_num']);
            $is_finish = interface_activity_is_finish($value['e_time'], $value['goods_status']); // 活动是否结束
            $event_info = array(
                'type_id' => $value['type_id'], // 服务分类
                'prices' => '￥' . $price_str,
                'unit' => $unit,
                'start_time' => '活动时间: ' . date('Y.m.d', $value['time_s']),
                'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220152&type=inner_app',
                'buy_num' => '已有' . $attend_num . '人参与',
                'seller' => $value['seller_name'],
                'seller_id' => $value['user_id'],
//                'seller_avatar' => $avatar,
                'is_show' => intval($value['is_show']),
                'is_finish' => $is_finish ? 1 : 0,  // 0 可参与 1 结束
                'sign' => $value['is_official'] == 1 ? '官方' : '',
                'sign_bg' => $value['is_official'] == 1 ? 1 : 0,
            );
            $goods_info = array_merge($goods_info, $event_info);
        }
        $goods[] = $goods_info;
    }
}
// 分享
$share = array();
if (version_compare($version, '3.2', '>')) {
    // 分享
    $share_img = $goods[0]['images'];
    $share = $task_goods_obj->get_list_share_text($pid, $return_query, $title, $share_img);
    // 促销信息
    $new_goods = array();
    $promotion_obj = POCO::singleton('pai_promotion_class');  // 促销
    foreach ($goods as $value) {
        $goods_id = $value['goods_id'];
        if ($goods_id < 1) {
            continue;
        }
        $pro_prices_list = $value['pro_prices_list'];  // 促销数据
        if (!empty($pro_prices_list)) {
            $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
            if (!empty($promotion)) {
                $promotion['abate'] = '立省: ' . $promotion['abate'];
                // 添加 促销信息
                $value = array_merge($value, $promotion);
            }
        }
        unset($value['pro_prices_list']);
        $new_goods[] = $value;
    }
    empty($new_goods) || $goods = $new_goods;
}


$goods_list = array(
    'title' => $title,
    'goods' => $goods,
    'total' => intval($total),
    'share' => $share,
    'tt_link' => 'yueyue://goto?type=inner_app&pid=1220080', // TT私人定制
);
$options['data'] = $goods_list;
return $cp->output($options);
