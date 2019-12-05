<?php
/**
 * 影棚租赁 初始化接口
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
$type_id = 12; // 影棚租赁
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
    $prices_data = $goods_info['prices_data']['1c383cd30b7c298ab50293adfecb7b18']['child_data'];
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
// 地理位置
$goods_location = $goods_info['goods_data']['location_id'];
$init_list['location_id'] = interface_init_goods_property_patch($goods_location, 'location_id');
$key_map = array(
    'common' => array(
        '9a96876e2f8f3dc4f3cf45f02c61c0c1' => 'classify',  // 场地类型
        '70efdf2ec9b086079795c442636b55fb' => 'type', // 可拍摄类型
        'titles' => 'titles', // 服务名称 default_data[titles]
        'location_id' => 'location_id',  // 地理位置
        '320722549d1751cf3f247855f937b982' => 'addr', // 详细地址
        '1f0e3dad99908345f7439f8ffabdffc4' => 'area', // 使用面积
        '98f13708210194c475687be6106a3b84' => 'background', // 背景
        '6f4922f45568161a8cdf4ad2299f6d23' => 'light', // 灯光/器材配套
    )
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
// 价格
$goods_init_info['standerd_title'] = '规格&价格';
$goods_init_info['standerd'] = interface_init_goods_property_patch($prices_data, 'standerd');
// 商品详情
$contents = interface_content_to_ubb($goods_info['default_data']['content']['value']);
// 上传配置
$upload_config = interface_services_upload_config($images, $contents, $type_id);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);