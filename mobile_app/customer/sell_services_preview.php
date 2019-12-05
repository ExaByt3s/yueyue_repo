<?php

/**
 * 服务商品 预览接口
 * 
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-9-11
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号
list($goods_id, $direct_order_id) = explode('_', $goods_id);   // 兼容 扫码立即下单
$request_platform = $client_data['data']['param']['request_platform'];   // 请求平台 ( for 兼容web 2015-7-29 )
$version = $client_data['data']['version'];  // 版本
$os_type = $client_data['data']['os_type'];  // 类型

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->show_goods_data_for_temp($goods_id);
if ($location_id == 'test') { //for debug
    $options['data'] = $goods_result;
    return $cp->output($options);
}
if ($goods_result['result'] != 1 && $goods_result['result'] != -3) {
    return $cp->output(array('data' => array()));
}
$goods = $goods_result['data'];
if (empty($goods)) {
    return $cp->output(array('data' => array()));
}
// 获取商品属性
$goods_property = interface_get_goods_property($goods['system_data'], $goods['goods_data']['location_id']);
$service = $goods_property['service'];
$guide = $goods_property['guide'];
$menu = $goods_property['menu'];;  // 服务内容
$recommend = $goods_property['recommend']; // 推荐
$guide_img = $goods_property['guide_img'];  // 导航
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
$type_id_arr = explode(',', $profile['type_id']);  // 用户认证  31 模特 40 摄影师
$user_name = $profile['name'];  // 商家名称
$user_avatar = $profile['avatar']; // get_user_icon($goods_user_id, 86, TRUE); // $profile['avatar'];  // 头像

$standard = array();  // 规格
foreach ($goods['prices_data_list'] as $value) {
    $prices = floatval($value['value']);
    if ($prices <= 0) {
        continue;
    }
    $standard[] = array(
        'id' => $value['id'],
        'name' => $value['name'],
        'value' => sprintf('%.2f', $prices),
        'original' => '',
        'num' => $value['num']
    );
    $min = ($prices < $min) ? $prices : (empty($min) ? $prices : $min);  // 最小价格
    $max = ($prices > $max) ? $prices : (empty($max) ? $prices : $max);   // 最大价格
}
// 价格范围
$range = empty($max) ? '' : ( $min == $max ? $max : sprintf('%.2f', $min) . '元 起');

$score = $goods['goods_data']['average_score']; // 综合评分
$score = intval($score) <= 0 ? 5 : $score;
// 商品详情
$contents = trim($goods['default_data']['content']['value']);
$contents = interface_content_replace_pics($contents, 640);  // 替换图片大小
if (!empty($contents) && $request_platform != 'web') {
    $contents = interface_content_to_ubb($contents);
}

$is_show = intval($goods['goods_data']['is_show']);  // 上下架情况
$type_id = intval($goods['goods_data']['type_id']);  // 商品分类
$type_name = $task_goods_obj->get_goods_typename_for_type_id($type_id);  // 获取分类名称
$type_name = empty($type_name) ? '' : '[' . $type_name . '] ';

$goods_info = array(
    'goods_id' => intval($goods['goods_data']['goods_id']),
    'is_show' => $is_show, // 是否在售: 1 在售 2 下架
    'show_str' => '商品预览',
    // 顶部 轮播图片
    'preview' => $preview,
    'zoom' => 'yueyue://goto?user_id=' . $user_id . '&pid=1250026&type=inner_app', // 点击放大
    'title' => $type_name . preg_replace('/&#\d+;/', '', $goods['default_data']['titles']['value']),
    'prices' => '￥' . (empty($range) ? $goods['default_data']['prices']['value'] : $range), // 价格区间
    'original_prices' => '', // 原始价格
    'favor' => array(
        'title' => '收藏',
        'value' => '0', // 1 已收藏
    ),
    // 规格
//    'standard' => $standard,
    'promise_title' => '服务内容',
    'promise' => $service, // 服务内容
    'user' => array(
        'user_id' => $goods_user_id,
        'name' => $user_name,
        'avatar' => $user_avatar,
        'homepage' => array(
            'title' => '商家主页',
            'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_user_id . '&pid=1220103&type=inner_app',
        ),
    ),
    'business' => array(
        'merit' => array('title' => '评价', 'value' => strval($score > 5 ? 5 : ( $score < 0 ? 0 : $score))), // 综合评价
        'totaltrade' => array('title' => '交易次数', 'value' => strval($goods['goods_data']['statistical']['bill_pay_num'])), // 交易次数
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_user_id . '&goods_id=' . $goods['goods_data']['goods_id'] . '&pid=1220075&type=inner_app', // 商品评价
    ),
    'profile_type' => $type_id, // 模特服务/摄影培训
    // 用户属性
    'property' => array(),
    // 图文详情
    'detail' => array(
        'title' => $goods['default_data']['content']['name'],
        'value' => $contents,
    ),
);
$att_data = $profile['att_data'];
$seller_property = interface_get_seller_property($profile['att_data']);  // 获取用户属性
foreach ($seller_property as $v) {
    $p_type_id = $v['type_id'];
    if ($p_type_id == $type_id) {
        $goods_info ['property'] = array($v);
        break;
    }
}

// for web
if ($request_platform == 'web') {
    $goods_info['standard'] = $standard;
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

$options['data'] = $goods_info; //
return $cp->output($options);
