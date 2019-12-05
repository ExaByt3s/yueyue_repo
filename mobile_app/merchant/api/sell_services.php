<?php

/**
 * 服务商品页
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号

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
// 获取商品属性
$type_id = intval($goods['goods_data']['type_id']);  // 商品分类
$status = intval($goods['goods_data']['status']);  // 状态
$goods_property = interface_get_goods_property($goods['system_data'], $goods['goods_data']['location_id']);
$service = $goods_property['service'];
$guide = $goods_property['guide'];
$menu = $goods_property['menu'];;  // 服务内容
$recommend = $goods_property['recommend']; // 推荐
$guide_img = $goods_property['guide_img'];  // 导航
$package = $goods_property['package'];  // 套餐
$preview = array(); // 图片
foreach ($goods['goods_data']['img'] as $value) {
    $img_url = $value['img_url'];
    $preview[] = array(
        'thumb' => yueyue_resize_act_img_url($img_url, '640'), // 缩略图
        'original' => yueyue_resize_act_img_url($img_url), // 原图
    );
}
$goods_user_id = $goods['goods_data']['user_id'];

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($goods_user_id, 2);  // 获取用户信息
if ($location_id == 'test1') { //for debug
    $options['data'] = $user_result;
    return $cp->output($options);
}
$profile = $user_result['seller_data']['profile'][0];
$type_id_arr = explode(',', $profile['type_id']);  // 用户认证
$user_name = $profile['name'];  // 商家名称
$user_avatar = $profile['avatar']; // get_user_icon($goods_user_id, 86, TRUE); // $profile['avatar'];  // 头像
$location = get_poco_location_name_by_location_id($user_result['seller_data']['location_id']);
$standard = array();  // 规格
$goods_prices = sprintf('%.2f', $goods['default_data']['prices']['value']); // 价格
$pro_prices_list = $goods_prices;
$prices_data_list = $goods['prices_data_list'];  // 价格列表
$package_keymap_ = array(  // 价格/属性图谱
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
        $standard_id = $value['id']; // 价格ID
        $standard_name = $value['name'];  // 价格名称
        $unit = ($type_id == 42) ? '' : $value['name_val'];  // 单位
        if ($type_id == 40) {  // 摄影服务
            $package_id = isset($package_keymap_[$standard_id]) ? $package_keymap_[$standard_id] : 0;
            if (isset($package[$package_id])) {
                $standard_name = $value['name_type'] . ':' . $package[$package_id];
                $unit = $package[$standard_id];
            } else {
                $unit = '';
            }
        }
        $unit = empty($unit) ? '' : $unit; // 单位
        if ($min <= 0 || bccomp($prices, $min, 2) < 0) { // 最小价格
            $min = $prices; // sprintf('%.2f', $prices);
            $prices_unit = $unit; // 单位
        }
        if ($max <= 0 || bccomp($prices, $max, 2) > 0) { // 最大价格
            $max = sprintf('%.2f', $prices);
        }
        // for 促销
        $pro_prices_list[] = array(
            'prices_type_id' => $value['id'], //必填
            'goods_prices' => sprintf('%.2f', $prices), //必填
            'prices_unit' => $unit,
        );
        if ($type_id == 42) {  // 活动价格
            $prices_list = $value['prices_list_data'];
            if (!empty($prices_list)) {
                $stage_id = $value['type_id'];  // 场次ID
                $show_standard = array();
                foreach ($prices_list as $v) {
                    $show_standard[] = array(
                        'stage_id' => $stage_id,
                        'id' => $v['id'],
                        'name' => $v['name'],
                        'value' => sprintf('%.2f', $v['prices']),
                        'original' => '',
                        'unit' => '',  // 单位
                        'num' => intval($v['stock_num']),  // 库存  stock_num_total
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
            'unit' => $unit,  // 单位
            'num' => $value['num']
        );
    }
}
// 价格范围
if (count($pro_prices_list) > 1) {
    $min = sprintf('%.2f', $min);
    $max = sprintf('%.2f', $max);
    $goods_prices = $min . '-' . $max;
    $prices_unit = '';
    $show_prices = $min . '-' . $max;
} else {
    $show_prices = $goods_prices;
    $prices_unit = ($type_id == 42) ? '' : '元';
}
//$score = $goods['goods_data']['average_score']; // 综合评分
$score = interface_reckon_average_score($goods['goods_data']['total_overall_score'], $goods['goods_data']['review_times']);
// 商品详情
$contents = trim($goods['default_data']['content']['value']);
$contents = interface_content_replace_pics($contents, 640);  // 替换图片大小
if (!empty($contents)) {
    $contents = interface_content_to_ubb($contents);
}
$is_show = intval($goods['goods_data']['is_show']);  // 上下架情况
$type_name = $task_goods_obj->get_goods_typename_for_type_id($type_id);  // 获取分类名称
$type_name = empty($type_name) ? '' : '[' . $type_name . '] ';

// 个人介绍
$introduce = array();
if ($type_id == 42) {
    $introduce = array(
        'title' => '组织者简介',
        'value' => interface_content_strip($user_result['seller_data']['introduce']),
    );
}
$titles = preg_replace('/&#\d+;/', '', $goods['goods_data']['titles']);  // 标题
$bill_pay_num = $goods['goods_data']['statistical']['bill_pay_num'];  // 交易次数
$goods_info = array(
    'goods_id' => $goods['goods_data']['goods_id'],
    'is_show' => $is_show, // 是否在售: 1 在售 2 下架
    'show_str' => $is_show == 2 ? '已下架' : '在售',
    // 顶部 轮播图片
    'preview' => $preview,
    'zoom' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250026&type=inner_app',
    'title' => $type_name . $titles,
    'prices' => '￥' . $show_prices, // 价格or价格区间
    'rate' => '￥' . $goods_prices,
    'unit' => $prices_unit,
    'original_prices' => '', // 原始价格
    'favor' => array(),
//    'standard' => $standard, // 规格
    'promise_title' => $type_id == 42 ? '活动安排' : '服务内容',
    'promise_more_title' => '更多内容',
    'promise' => $service, // 服务内容
    'user' => array(
        'title' => ($type_id == 42) ? '组织者信息' : '商家资料',
        'user_id' => $goods_user_id,
        'name' => $user_name,
        'avatar' => $user_avatar,
        'location' => str_replace(' ', '·', $location),  // 中间用点
        'introduce' => $introduce,   // 个人简介
        'homepage' => array(
            'title' => '商家主页',
            'request' => 'yueseller://goto?user_id=' . $goods_user_id . '&pid=1250004&type=inner_app',
        ),
    ),
    'business' => array(
        'merit' => array('title' => '服务评价', 'value' => strval($score)), // 综合评价
        // 交易次数
        'totaltrade' => array(
            'title' => '交易次数',
            // $goods['goods_data']['review_times'] 评价次数
            'value' => $goods['goods_data']['statistical']['bill_pay_num'],
            // 商品评价
            'request' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods['goods_data']['goods_id'] . '&pid=1250009&type=inner_app',
        ),
        // for 兼容android版本(下版本弃用)
        'request' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods['goods_data']['goods_id'] . '&pid=1250009&type=inner_app',
    ),
    'profile_type' => $type_id, // 服务类型
    // 用户属性
    'property' => array(),
    // 图文详情
    'detail' => array(
        'title' => $type_id == 42 ? '活动详情' : $goods['default_data']['content']['name'],
        'value' => $contents,
    ),
    // 分享
    'share' => $task_goods_obj->get_share_text($goods_id),
);

// 1.1 版本支持的 服务编辑功能
if (($type_id == 31 && version_compare($version, '1.1', '>')) ||
    (!in_array($type_id, array(42, 43)) && version_compare($version, '1.2', '>'))
) {
    $status = $goods['goods_data']['status']; // 状态 0 未审核 1 通过 2未通过 3删除
    $status_arr = array(
        0 => 'review',
        1 => 'pass',
        2 => 'rejected',
        3 => 'remove',
    );
    $goods_info['status_str'] = $status_arr[$status];  // 服务状态
    // 添加编辑链接
    $goods_info['edit_url'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&operate=edit&pid=1250028&type=inner_app';
    // 拒绝原因
    $task_log_obj = POCO::singleton('pai_task_admin_log_class');
    $notice = '';
    if ($status == 2) {
        $reject_data = array('type_id' => 2007, 'action_type' => 2, 'action_id' => $goods_id);
        $log_result = $task_log_obj->get_log_by_type_last($reject_data);  // 拒绝原因
        $notice = trim(preg_replace('/\d+:/', '', $log_result['note']));
    }
    $goods_info['notice'] = $notice;
}

$att_data = $profile['att_data'];
$seller_property = interface_get_seller_property($profile['att_data']);  // 获取用户属性
foreach ($seller_property as $v) {
    $p_type_id = $v['type_id'];
    if ($p_type_id == $type_id) {
        $goods_info ['property'] = array($v);
        break;
    }
}
// 约活动
if ($type_id == 42) {
    // 经纬度
    $lng_lat = $goods['goods_data']['lng_lat'];
    list($lng, $lat) = explode(',', $lng_lat);
    $goods_info['grid'] = array(
        'title' => '经纬度(lng经度,lat纬度)',
        'lng' => floatval($lng),
        'lat' => floatval($lat),
    );
    // 活动标识
    $is_official = intval($goods['goods_data']['is_official']);
    $goods_info['mark'] = array(
        'sign' => $is_official == 1 ? '官方' : '',
        'sign_bg' => $is_official == 1 ? 1 : 0,
    );
    // 活动领队
    $contact_data = $goods['contact_data'];
    $contact_str = '';
    foreach ($contact_data as $contact) {
        $data = $contact['data'];
        $contact_str .= $data['name'] . ' ' . $data['phone'] . "\n";
    }
    if (!empty($contact_str)) {
        $goods_info['promise'][] = array(
            'id' => 'contact_data',
            'title' => '活动领队：',
            'value' => trim($contact_str),
        );
    }
    // 查看场次
    $exhibit = interface_get_goods_showing($prices_data_list);
    $showing_exhibit = array();
    foreach ($exhibit['showing_exhibit'] as $showing) {
        // 订单链接
        $new_order_url = $order_url . '&stage_id=' . $showing['stage_id'];
        $showing['request'] = 'yueyue://goto?type=inner_web&url=' . urlencode($new_order_url) . '&wifi_url=' . urlencode($new_order_url) . '&showtitle=1';
        $showing_exhibit[] = $showing;
    }
    $goods_info['showing'] = array(
        'title' => '活动场次',
        'more_title' => '查看更多场次',
        'is_finish' => $exhibit['is_finish'],  // 0进行中 ，1 已结束
        'attend' => $exhibit['all_exhibit'][0],
        'exhibit' => $showing_exhibit,
    );
    if (empty($exhibit['showing_exhibit'])) {  // 全部结束
        $goods_info['showing']['is_finish'] = 1;
        $goods_info['showing']['closure'] = '该活动已结束';
        $goods_info['showing']['attend'] = end($exhibit['all_exhibit']);
        $goods_info['show_str'] = $is_show == 2 ? '已下架' : '已结束';  // 活动
    } elseif ($exhibit['is_finish'] == 1) { // 全部场次已报满
        $goods_info['show_str'] = $is_show == 2 ? '已下架' : '已结束';  // 活动
    }
    // 报名名单
    $roster_list = array();
    foreach ($exhibit['all_exhibit'] as $exhibit) {
        $exhibit['request'] = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id .
            '&stage_id=' . $exhibit['stage_id'] . '&pid=1250045&type=inner_app'; // 商品评价
        $roster_list[] = $exhibit;
    }
    $goods_info['roster'] = array(
        'title' => '报名名单',
        'value' => $roster_list,
    );
    // 活动回顾
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
        'title' => '活动回顾',
        'value' => $review_content,
//        'request' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=&type=inner_app', // 商品评价
    );
}
// 约美食
if ($type_id == 41) {
    $goods_info['recommend'] = array(
        'title' => '达人推荐原因',
        'value' => $recommend,
    );
    $goods_info['menu'] = array(
        'title' => $menu['title'],
        'value' => implode("\r\n", $menu['value']),
    );
    $goods_info['guide'] = array(
        'title' => '餐厅信息',
        'list' => $guide,
        'img' => $guide_img,
    );
}
// 1.2.0 促销  2015-10-12
if (version_compare($version, '1.2', '>=')) {
    // 促销列表
    $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, null, true);
    $promotion_list = array();
    if (!empty($promotion)) {
        $promotion['title'] = '促销活动';
        $promotion['abate'] = '最高可省：￥' . sprintf('%.2f', $promotion['abate']);
        $promotion['notice'] .= '：';
        $promotion_list = $promotion['promotion_list'];
        unset($promotion['promotion_list']);
    }
    $goods_info['promotion'] = $promotion;
    // 价格列表
    $prices_list = array();
    foreach ($pro_prices_list as $prices) {
        $prices_type_id = $prices['prices_type_id'];
        $prices_promotion = isset($promotion_list[$prices_type_id]) ? $promotion_list[$prices_type_id] : array();
        $prices_list[] = array_merge(array(
            'id' => $prices_type_id,
            'prices' => '￥' . $prices['goods_prices'],
            'unit' => empty($prices['prices_unit']) ? '' : '/' . $prices['prices_unit'],
        ), $prices_promotion);
    }
    $goods_info['prices_list'] = $prices_list;
}

$options['data'] = $goods_info; //
return $cp->output($options);
