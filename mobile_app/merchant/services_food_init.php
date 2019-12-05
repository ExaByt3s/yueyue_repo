<?php
/**
 * 美食达人 初始化
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-10-14
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$options['data'] = array('result' => 0, 'message' => '暂不支持编辑');
return $cp->output($options);

include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号
$operate = $client_data['data']['param']['operate'];   // 操作类型  edit/add
$type_id = 41; // 美食
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
    $prices_data = array();
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
$init_list['f7664060cc52bc6f3d620bcedc94a4b6']['title'] = '推荐原因'; // 修改标题
// 封面图
$images = $goods_info['goods_data']['images'];
$init_list['goods_id'] = $goods_info['goods_data']['goods_id'];
// 标题
$title = $goods_info['default_data']['titles']['value'];
$init_list['titles'] = array(
    'id' => 'titles',
    'title' => '服务名称',
    'value' => empty($title) ? '' : $title,
    'input_type' => '1',
    'max_length' => 30,
    'hint' => '请输入服务名称',
    'unit' => '',
);
// 地理位置
$goods_location = $goods_info['goods_data']['location_id'];
$city = get_poco_location_name_by_location_id($goods_location);  // 城市
$init_list['location_id'] = array(
    'id' => 'location_id',
    'title' => '所在地区',
    'value' => $city,
    'option_value' => empty($goods_location) ? '' : $goods_location, // 入库的值
    'input_type' => '2',
    'hint' => '',
    'unit' => '',
);
$key_map = array(
    'common' => array(
        'titles' => 'titles', // 服务名称 default_data[titles]
        'c0e190d8267e36708f955d7ab048990d' => 'cooking', // 菜系标签
        '077e29b11be80ab57e1a2ecabb7da330' => 'menu', // 基本菜单
        '502e4a16930e414107ee22b6198c578f' => 'name', // 餐厅名称
        'ec8ce6abb3e952a85b8551ba726a1227' => 'environment', // 环境标签
        'location_id' => 'location_id',  // 所在地区
        'cfa0860e83a4c3a763a7e62d825349f7' => 'addr', // 餐厅地址
        'a4f23670e1833f3fdb077ca70bbd5d66' => 'tel', // 联系电话
//        '6c9882bbac1c7093bd25041881277658' => 'num', // 用餐人数
        'b1a59b315fc9a3002ce38bbe070ec3f5' => 'nav_way', // 导航方式
//        'e56954b4f6347e897f954495eab16a88' => 'nav_img', // 导航图片
        'eda80a3d5b344bc40f3bc04f65b7a357' => 'request', // 预约要求
        '8f121ce07d74717e0b1f21d122e04521' => 'remark', // 温馨提示
        'f7664060cc52bc6f3d620bcedc94a4b6' => 'recommend',  // 推荐原因(不吃是一种罪)
    )
);
$nav_img = $init_list['e56954b4f6347e897f954495eab16a88']['value']; // 导航图片
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
// 商品详情
$contents = interface_content_to_ubb($goods_info['default_data']['content']['value']);
// 价格 ( 套餐 )
foreach ($prices_data as $value) {
    preg_match('/(.*?)（(.*?)人）$/', $value['name'], $match);
    $title = $match[1];
    $share_num = $match[2];
    $items = array(
        array('type' => '4', 'id' => 'name', 'title' => '套餐名', 'max_length' => 15, 'value' => $title, 'input_type' => 1, 'hint' => '请输入套餐名称', 'unit' => '',),
        array('type' => '4', 'id' => 'share_num', 'title' => '用餐人数', 'value' => $share_num, 'input_type' => 2, 'hint' => '', 'unit' => '人',),
        array('type' => '4', 'id' => 'price', 'title' => '价格', 'value' => $value['prices'], 'input_type' => 3, 'hint' => '', 'unit' => '元',),
        array('type' => '4', 'id' => 'stock_num', 'title' => '库存', 'value' => $value['stock_num_total'], 'input_type' => 2, 'hint' => '', 'unit' => '份',),
    );
    $prices_list[] = array(
        'id' => $value['type_id'],
        'title' => $title,
        'value' => $value['prices'],
        'input_type' => 1,
        'hint' => '编辑',
        'unit' => '',
        'options' => $items,
    );
}
$goods_init_info['standerd_title'] = '套餐规格';
$goods_init_info['standerd'] = $prices_list;
$goods_init_info['standerd_max'] = 5;
$goods_init_info['package'] = array(
    'id' => '',
    'title' => '默认套餐',
    'value' => '',
    'input_type' => 1,
    'hint' => '编辑',
    'unit' => '',
    'options' => array(
        array('type' => '4', 'id' => 'name', 'title' => '套餐名', 'max_length' => 15, 'value' => '', 'input_type' => 1, 'hint' => '请输入套餐名称', 'unit' => '',),
        array('type' => '4', 'id' => 'share_num', 'title' => '用餐人数', 'value' => '', 'input_type' => 2, 'hint' => '', 'unit' => '人',),
        array('type' => '4', 'id' => 'price', 'title' => '价格', 'value' => '', 'input_type' => 3, 'hint' => '', 'unit' => '元',),
        array('type' => '4', 'id' => 'stock_num', 'title' => '库存', 'value' => '', 'input_type' => 2, 'hint' => '', 'unit' => '份',),
    )
);
// 上传配置
$addon = array(
    // 导航图片
    'guide' => empty($nav_img) ? array() : explode(',', $nav_img),
    'post_guide' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_guide_wifi' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'guide_size' => 640,
    'guide_num' => 8,
    'guide_tips' => '请上传导航图片',
);
$upload_config = interface_services_upload_config($images, $contents, $type_id, 20, $addon);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);