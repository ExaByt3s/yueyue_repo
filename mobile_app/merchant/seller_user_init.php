<?php
/**
 * 商家编辑
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015/10/16
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // 获取用户信息
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$profile = $user_result['seller_data']['profile'][0];
if (empty($profile)) {
    $result = array('result' => 0, 'message' => '没有该商家');
    $options['data'] = $result;
    return $cp->output($options);
}
$type_id = $profile['type_id'];  // 用户认证   3 化妆,31 模特,40 摄影师,12 影棚,5 培训
$location_id = $profile['location_id'];  // 城市ID
$city = get_poco_location_name_by_location_id($location_id);  // 城市
// 图文详情
$contents = interface_content_to_ubb($profile['introduce']);
// 认证属性
$property = $attr_list = $bwh_arr = array();
$att_data = $profile['att_data'];
foreach ($att_data as $val) {
    $key = $val['key'];
    if (in_array($key, array('ms_forwarding', 'p_order_income', 'p_team',
        'm_level', 'm_sex', 'yp_place', 'yp_background', 'yp_can_photo', 'yp_lighter',
        'yp_other_equitment', 'hz_order_way', 'hz_place', 't_way',))) {
        // 不需要的属性
        continue;
    }
    $value = empty($val['value']) ? '' : $val['value'];
    if (in_array($key, array('m_height', 'm_weight', 'm_cups', 'm_cup', 'm_bwh'))) {
        // 模特数据
        if ($key == 'm_bwh') {  // 三围特殊处理
            list($m_bwh_chest, $m_bwh_waist, $m_bwh_hip) = explode('-', $value);
            $bwh_arr['m_bwh_chest'] = $m_bwh_chest;
            $bwh_arr['m_bwh_waist'] = $m_bwh_waist;
            $bwh_arr['m_bwh_hip'] = $m_bwh_hip;
            continue;
        }
        $bwh_arr[$key] = $value;
        continue;
    }
    $hint = '编辑';
    $att_type = '4';
    $choice_type = '0'; // 选项
    $choice_num = 0;
    $input_type = 2;
    if (in_array($key, array('m_cup', 'hz_othergoodat', 'ot_otherlabel', 'ev_other'))) {
        $input_type = 1;
    }
    $options = array();
    $child_data = $val['child_data'];
    if (!empty($child_data)) {
        $hint = '请选择';
        $att_type = '2';
        $choice_num = 1;
        $input_type = 0;
        $choice_type = '1'; // 单选
        if (in_array($key, array('ot_label', 'hz_goodat'))) {
            $att_type = '5';  //标签选择
            $choice_type = '2';
            $choice_num = 3;
        }
        foreach ($child_data as $child) {
            $options[] = array(
                'value' => $child,  // 显示标题
                'option_value' => $child,  // 入口的值
            );
        }
    }
    $type_k = substr($key, 0, strpos($key, '_'));
    $attr_list[$type_k][] = array(
        'type' => $att_type, //控件类型，1、跳页选择； 2、弹框选择；3、弹页输入；4、当前输入；5、标签选择
        'id' => $key,  // key
        'title' => $val['name'],  // 显示的标题
        'value' => str_replace(',', '-', $value),  // 显示的值
        'option_value' => $value, // 入库的值
        'choice_type' => $choice_type,  // 选项级别, 1单选,2多选
        'choice_num' => $choice_num, // 最多选中几个
        'input_type' => $input_type,  // 文字1,数字2
        'options' => $options,
        'hint' => $hint,
        'unit' => '',
    );
}
$type_list_ = interface_type_list();  // 品类列表
// 品类图标 ( 3 化妆,31 模特,40 摄影师,12 影棚,5 培训,41 美食,43 其他服务 )
$style_keymap_ = array(
    'm' => 31,
    'yp' => 12,
    't' => 5,
    'p' => 40,
    'hz' => 3,
    'ms' => 41,
    'ot' => 43,
    'ev' => 42,
);
$type_id_arr = explode(',', $type_id);  // 认证属性
foreach ($style_keymap_ as $k => $show_type_id) {
    $v = $type_list_[$show_type_id];
//    $v['title'] = $v['title'] . '商家';
    if (!in_array($show_type_id, $type_id_arr)) {
        continue;
    }
    $att_info = isset($attr_list[$k]) ? $attr_list[$k] : array();
    if ($k == 'm') { // 模特专有属性
        $v['item']['bwh'] = $bwh_arr;
    }
    $v['item']['description'] = $att_info;
    $property[] = $v;
}
$user_info = array(
    'user_id' => $profile['user_id'], // 用户ID
    'profile_id' => $profile['seller_profile_id'], // 属性ID
    'type_id' => $type_id,
    'common' => array(
        'name' => array(
            'type' => '4',
            'id' => 'name',  // key
            'title' => '昵称',  // 显示标题
            'value' => $profile['name'],  // 显示的值
            'option_value' => '', // 入库的值
            'input_type' => '1',  // 文字1,数字2
            'hint' => '请输入昵称',
            'unit' => '',
        ),
        'location_id' => array(
            'type' => '2',
            'id' => 'location_id',
            'title' => '城市',
            'value' => $city,
            'option_value' => $location_id, // 入库的值
            'input_type' => '2',
            'hint' => '',
            'unit' => '',
        ),
        'introduce' => array(
            'type' => '3',  // 控件类型，1、跳页选择； 2、弹框选择；3、弹页输入；4、当前输入
            'id' => 'introduce',
            'title' => '个人介绍',
            'value' => '',
            'option_value' => '', // 入库的值
            'input_type' => '2',
            'hint' => '编辑详情',
            'unit' => '',
        ),
    ),
    // 属性
    'property' => $property,
);

// 上传配置
$upload_config = array(
    'avatar' => get_seller_user_icon($user_id, 165, TRUE), // $profile['avatar'], // 头像
    'post_icon' => 'http://sendmedia-w.yueus.com:8078/icon.cgi',
    'post_icon_wifi' => 'http://sendmedia-w-wifi.yueus.com:8078/icon.cgi',
    'icon_size' => 640,
    'cover' => yueyue_resize_act_img_url($profile['cover'], '640'), // 背景图
    'post_cover' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_cover_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'cover_size' => 640,
    'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_pic_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'pic_size' => 640,
    'pic_num' => 15,
    'contents' => $contents,
);

$options['data'] = array_merge($user_info, $upload_config);
return $cp->output($options);