<?php
/**
 * 其他 初始化接口
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-10-8
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号
$operate = $client_data['data']['param']['operate'];   // 操作类型  edit/add
$type_id = 43; // 其他（约有趣）
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
    $prices_data = $goods_info['prices_data']['d395771085aab05244a4fb8fd91bf4ee']['child_data'];
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
// 获取属性
$goods_property = interface_init_goods_property($goods_info['system_data']);
$init_list = $goods_property['init_list'];
$first_options = $goods_property['first_options'];  // 一级选项
$second_options = $goods_property['second_options'];
// 封面图
$images = $goods_info['goods_data']['images'];
$init_list['goods_id'] = $goods_info['goods_data']['goods_id'];
// 标题
$title = $goods_info['default_data']['titles']['value'];
$init_list['titles'] = interface_init_goods_property_patch($title, 'titles');
$init_list['prices'] = array(
    'type' => '4', //控件类型
    'id' => 'default_data.prices', // 入库键值
    'title' => '价格', // 显示标题
    'value' => intval($goods_info['default_data']['prices']['value']), // 显示值
    'option_value' => '', // 入库值
    'input_type' => '3', // 输入类型 1 文字 2 数字 3 小数
    'max_length' => 0,  // 最大输入个数
    'hint' => '',
    'unit' => '元',
    'tips' => '请填写价格',
);
// 地理位置
$goods_location = $goods_info['goods_data']['location_id'];
$init_list['location_id'] = interface_init_goods_property_patch($goods_location, 'location_id');
$key_map = array(
    'common' => array(
        '07cdfd23373b17c6b337251c22b7ea57' => 'type', // 服务关键词
        'fb7b9ffa5462084c5f4e7e85a093e6d7' => 'other_tags', // 其他标签
        'titles' => 'titles', // 服务名称 default_data[titles]
        'a8abb4bb284b5b27aa7cb790dc20f80b' => 'classify',  // 服务类型
        'prices' => 'prices',  // 价格
        'location_id' => 'location_id',  // 地理位置
        'd709f38ef758b5066ef31b18039b8ce5' => 'address',  //地址
        '1651cf0d2f737d7adeab84d339dbabd3' => 'refund',  // 退费规则
        '0e01938fc48a2cfb5f2217fbfb00722d' => 'attention',  //注意事项
    )
);
if (version_compare($version, '1.3', '>')) {
    $init_list['location_id']['type'] = 6;  // 6地理位置
    // 课程类
    $class_options = $init_list['550a141f12de6341fba65b0ad0433500']['options'];
    $init_list['d709f38ef758b5066ef31b18039b8ce5']['title'] = '上课地点';  // 地址
    $init_list['eed5af6add95a9a6f1252739b1ad8c24']['title'] = '课程描述';
    $init_list['550a141f12de6341fba65b0ad0433500']['options'] = array_merge(
        array(
            $init_list['location_id'],  // 地理位置
            $init_list['d709f38ef758b5066ef31b18039b8ce5'],  // 地址
            $init_list['eed5af6add95a9a6f1252739b1ad8c24'],  // 描述
        ), $class_options
    );
    // 商品类
    $goods_options = $init_list['67f7fb873eaf29526a11a9b7ac33bfac']['options'];
    $init_list['eed5af6add95a9a6f1252739b1ad8c24']['title'] = '商品描述';
    $init_list['67f7fb873eaf29526a11a9b7ac33bfac']['options'] = array_merge(
        array(
            $init_list['eed5af6add95a9a6f1252739b1ad8c24'],  // 描述
        ), $goods_options
    );
    // 服务类
    $services_options = $init_list['1a5b1e4daae265b790965a275b53ae50']['options'];
    $init_list['d709f38ef758b5066ef31b18039b8ce5']['title'] = '服务地点';  // 地址
    $init_list['eed5af6add95a9a6f1252739b1ad8c24']['title'] = '服务描述';
    $init_list['1a5b1e4daae265b790965a275b53ae50']['options'] = array_merge(
        array(
            $init_list['location_id'],  // 地理位置
            $init_list['d709f38ef758b5066ef31b18039b8ce5'],  // 地址
            $init_list['eed5af6add95a9a6f1252739b1ad8c24'],  // 描述
        ), $services_options
    );
    // 1.3 版本 ,无需地址, 描述
    unset($init_list['location_id'], $init_list['d709f38ef758b5066ef31b18039b8ce5'], $init_list['eed5af6add95a9a6f1252739b1ad8c24']);
}
// 处理名称问题 ( 服务标签 -> 服务关键词 )
$init_list['07cdfd23373b17c6b337251c22b7ea57']['title'] = '服务关键词';
$init_list['a8abb4bb284b5b27aa7cb790dc20f80b']['title'] = '服务类型';
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
if (version_compare($version, '1.3', '>')) {
    // 定义的三种属性
    $property_keymap = array(
        '550a141f12de6341fba65b0ad0433500' => '15d4e891d784977cacbfcbb00c48f133',  // 课程类属性
        '67f7fb873eaf29526a11a9b7ac33bfac' => 'c203d8a151612acf12457e4d67635a95',  // 商品类属性
        '1a5b1e4daae265b790965a275b53ae50' => '13f3cf8c531952d72e5847c4183e6910',  // 服务类属性
    );
    $property_option = array();
    foreach ($property_keymap as $key => $value) {
        $property_option[] = array(
            'property_type' => $value,
            'property_list' => $init_list[$key]['options'],
        );
    }
    $goods_init_info['property'] = $property_option;
}
$goods_init_info['options'] = array(
    'first' => $first_options_init,
    'second' => $second_options,
);
if (version_compare($version, '1.3', '<')) {
    // 价格
    $goods_init_info['standerd_title'] = '规格&价格';
    $goods_init_info['standerd'] = interface_init_goods_property_patch($prices_data, 'standerd');
}
// 商品详情
$contents = interface_content_to_ubb($goods_info['default_data']['content']['value']);
// 上传配置
$upload_config = interface_services_upload_config($images, $contents, $type_id);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);