<?php
/**
 * 摄影服务 初始化接口
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-28
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号
$operate = $client_data['data']['param']['operate'];   // 操作类型  edit/add
$type_id = 40; // 摄影服务
$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
if ($operate == 'edit') {
    if (empty($goods_id)) {
        $options['data'] = array(); //
        return $cp->output($options);
    }
    $goods_result = $mall_goods_obj->user_get_goods_info($goods_id, $user_id);
    if ($goods_result['result'] != 1) {
        return $cp->output(array('data' => array()));
    }
    $goods_info = $goods_result['data'];
    $prices_data = $goods_info['prices_data_list'];
} else if ($operate == 'add') {
    $goods_result = $mall_goods_obj->user_show_goods_data($type_id);
    $goods_info = $goods_result['data'];
    $prices_data = $goods_info['prices_data']['eddea82ad2755b24c4e168c5fc2ebd40']['child_data'];
} else {
    $options['data'] = array(); //
    return $cp->output($options);
}
if ($location_id == 'test') {
    $options['data'] = array(
        '$operate' => $operate,
        '$goods_result' => $goods_result,
    );
    return $cp->output($options);
}
if (empty($goods_info)) {
    $options['data'] = array(); //
    return $cp->output($options);
}
// 价格
$prices_list = array();
foreach ($prices_data as $value) {
    $id = $value['id'];
    $prices_list[$id] = array(
        'type' => '4',
        'id' => 'prices_de.' . $id,
        'title' => '套餐价格',
        'value' => empty($value['value']) ? '' : $value['value'],
        'input_type' => '3',
        'item_split' => '1',
        'hint' => '价格',
        'unit' => '元',
    );
}
// 获取属性
$goods_property = interface_init_goods_property($goods_info['system_data'], $prices_list);
$init_list = $goods_property['init_list'];
$first_options = $goods_property['first_options'];  // 一级选项
$second_options = $goods_property['second_options'];
// 封面图
$images = $goods_info['goods_data']['images'];
$init_list['goods_id'] = $goods_info['goods_data']['goods_id'];
// 标题
$title = $goods_info['default_data']['titles']['value'];
$init_list['titles'] = interface_init_goods_property_patch($title, 'titles');
// 地理位置
$goods_location = $goods_info['goods_data']['location_id'];
$init_list['location_id'] = interface_init_goods_property_patch($goods_location, 'location_id');
$init_list['ad13a2a07ca4b7642959dc0c4c740ab6']['hint'] = '必填';
$init_list['758874998f5bd0c393da094e1967a72b']['hint'] = '必填';
$init_list['3fe94a002317b5f9259f82690aeea4cd']['hint'] = '选填';
if (version_compare($version, '1.3', '>')) {
    // 拍摄类型 -> 场地类型
    $init_list['70efdf2ec9b086079795c442636b55fb']['title'] = '场地类型';
} else {
    // 处理 拍摄分类
    $shoot_style_arr = explode('-', $init_list['8613985ec49eb8f757ae6439e879bb2a']['option_value']);
    if (!empty($shoot_style_arr)) {
        $shoot_style = isset($shoot_style_arr[1]) ? $shoot_style_arr[1] : $shoot_style_arr[0];
        $init_list['8613985ec49eb8f757ae6439e879bb2a']['option_value'] = $shoot_style;
    }
}

$key_map = array(
    'common' => array(
        'titles' => 'titles', // 服务名称 default_data[titles]
        'location_id' => 'location_id',  // 地理位置
        '98dce83da57b0395e163467c9dae521b' => 'address', // 拍摄地点
        'f4b9ec30ad9f68f89b29639786cb62ef' => 'description', // 主要描述
        '8613985ec49eb8f757ae6439e879bb2a' => 'style', // 拍摄分类
        '839ab46820b524afda05122893c2fe8e' => 'tags', // 标签
    ),
    'package' => array(
        'ad13a2a07ca4b7642959dc0c4c740ab6' => 'normal', // 标配服务套餐内容
        '758874998f5bd0c393da094e1967a72b' => 'originality', // 创意裸价套餐内容
        '3fe94a002317b5f9259f82690aeea4cd' => 'customization', // 尊享服务套餐内容
    ),
);
$goods_init_info['goods_id'] = $goods_id;  // 初始化数据
foreach ($key_map as $key => $map) {
    foreach ($map as $k => $v) {
        $value = $init_list[$k];
        if (empty($value)) {
            continue;
        }
        $goods_init_info[$key][$v] = $value;  // key转名称
        // 组装 选项
        if (isset($first_options[$k])) {
            $first_options_init[$v . '_options'] = $first_options[$k];
        }
    }
}
$goods_init_info['options'] = array(
    'first' => $first_options_init,
    'second' => $second_options,
);
// 商品详情 ( 图片 )
$contents = trim($goods_info['default_data']['content']['value']);
$pic_list = array();
if (!empty($contents)) {
    $img_list = interface_grab_content_images($contents, 640);  // 获取图片
    foreach ($img_list as $img) {
        if (empty($img) || in_array($img, $pic_list)) {
            continue;
        }
        $pic_list[] = $img;
    }
}
// 套餐
$goods_init_info['standerd_title'] = '服务套餐';
// 上传配置
$addon = array(
    'pic_min' => 5,
    'pic_arr' => $pic_list,
    'pic_tips' => '请上传不少于5张样图',
);
$upload_config = interface_services_upload_config($images, null, $type_id, 20, $addon);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);