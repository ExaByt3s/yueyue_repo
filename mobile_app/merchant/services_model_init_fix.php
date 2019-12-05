<?php
/**
 * 模特服务初始化
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-24
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号
$operate = $client_data['data']['param']['operate'];   // 操作类型  edit/add
$type_id = 31; // 模特
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
    $prices_data = $goods_info['prices_data']['d09bf41544a3365a46c9077ebb5e35c3']['child_data'];
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
// 风格分类 (三种分类)
$style_keymap = array(
    '67c6a1e7ce56d3d6fa748ab6d9af3fd7' => 1, // 欧美
    '642e92efb79421734881b53e1e1b18b6' => 1, // 情绪
    'f457c545a9ded88f18ecee47145a72c0' => 1, // 糖水
    'c0c7c76d30bd3dcaefc96f40275bdc0a' => 1, // 古装
    '2838023a778dfaecdc212708f721b788' => 1, // 日韩
    '9a1158154dfa42caddbd0694a4e9bdc8' => 1, // 文艺复兴
    'a684eceee76fc522773286a895bc8436' => 1, // 内衣/比基尼
    'b53b3a3d6ab90ce0268229151c9bde11' => 1, // 甜美
    '1afa34a7f984eeabdbb0a7d494132ee5' => 1, // 真空
    '9f61408e3afb633e50cdf1b20de6f466' => 2, // 礼仪
    'd1f491a404d6854880943e5c3cd9ca25' => 2, // 车展
    '9b8619251a19057cff70779273e95aa6' => 2, // 走秀
    '72b32a1f754ba1c09b3695e0cb6cde7f' => 3, // 淘宝
);
// 获取属性
$goods_property = interface_init_goods_property($goods_info['system_data']);
$init_list = $goods_property['init_list'];
$first_options = $goods_property['first_options'];  // 以及选项
$second_options = $goods_property['second_options'];
$option_value = $init_list['d9d4f495e875a2e075a1a4a6e1b9770f']['option_value'];
$init_list['d9d4f495e875a2e075a1a4a6e1b9770f']['style_type'] = isset($style_keymap[$option_value]) ? $style_keymap[$option_value] : 1;
foreach ($first_options as $key => $options) {
    if ($key == 'd9d4f495e875a2e075a1a4a6e1b9770f') { // 风格
        foreach ($options as $k => $val) {
            $option_value = $val['option_value'];
            $options[$k]['style_type'] = isset($style_keymap[$option_value]) ? $style_keymap[$option_value] : '';
        }
    }
    $first_options[$key] = $options;
}
// 封面图
$images = $goods_info['goods_data']['images'];
$init_list['goods_id'] = $goods_info['goods_data']['goods_id'];
// 标题
$title = $goods_info['default_data']['titles']['value'];
$init_list['titles'] = interface_init_goods_property_patch($title, 'titles');
$key_map = array(
    'common' => array(
        'titles' => 'titles', // 服务名称 default_data[titles]
        'd9d4f495e875a2e075a1a4a6e1b9770f' => 'style_name', // 拍摄风格
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
// 起拍件数 ( for 淘宝 )
$prices_up = $init_list['16a5cdae362b8d27a1d8f8c7b78b4330'];
if (!empty($prices_up)) {
    $prices_up['hint'] = '件数';
    $prices_list['taobao'] = array($prices_up);
}
// 价格
$prices_rule = array(
    //约拍: 2 , 4 , 1天
    'yuepai' => array(76, 77, 87),
    //商业: 半天,一天
    'trade' => array(287, 87),
    //淘宝: 一件,半天,一天
    'taobao' => array(288, 287, 87),
);
foreach ($prices_data as $value) {
    $id = $value['id'];
    foreach ($prices_rule as $k => $v) {
        if (!in_array($id, $v)) {
            continue;
        }
        $prices_list[$k][] = array(
            'type' => '4',
            'id' => $id,
            'title' => empty($value['name_val']) ? $value['name'] : $value['name_val'],
            'value' => empty($value['value']) ? '' : $value['value'],
            'input_type' => '3',
            'hint' => '价格',
            'unit' => '元',
            'tips' => '请输入服务价格',
        );
    }
}
// 定义的规格(三种)
$standerd_option = array(
    array(
        'style_type' => '1',
        'prices_list' => $prices_list['yuepai'], // 约拍
    ),
    array(
        'style_type' => '2',
        'prices_list' => isset($prices_list['trade']) ? $prices_list['trade'] : $prices_list['yuepai'], // 商业
    ),
    array(
        'style_type' => '3',
        'prices_list' => isset($prices_list['taobao']) ? $prices_list['taobao'] : $prices_list['yuepai'], // 淘宝
    ),
);
$goods_init_info['standerd_title'] = '规格&价格';
$goods_init_info['standerd'] = $standerd_option;
// 商品详情
$contents = interface_content_to_ubb($goods_info['default_data']['content']['value']);
// 上传配置
$upload_config = interface_services_upload_config($images, $contents, $type_id);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);