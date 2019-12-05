<?php
/**
 * 培训提交
 *
 * @author willike<chenwb@yueus.com>
 * @since 2015-9-28
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = intval($client_data['data']['param']['goods_id']);   // 商品ID
$common = $client_data['data']['param']['common'];
$choice = $client_data['data']['param']['choice'];
$cover = $client_data['data']['param']['cover'];  // 背景图
$content = $client_data['data']['param']['content'];  // 图文详情
if (empty($user_id) || empty($common) || empty($choice) || empty($cover)) {
    $result = array('result' => 0, 'message' => '数据未完整!');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($content)) {
    $result = array('result' => -1, 'message' => '图文详情为空');
    $options['data'] = $result;
    return $cp->output($options);
}
foreach ($cover as $img_val) {
    if (empty($img_val)) {
        continue;
    }
    $imgs[] = array('img_url' => $img_val);
}
if (empty($imgs)) {
    $result = array('result' => -2, 'message' => '背景图为空');
    $options['data'] = $result;
    return $cp->output($options);
}
// 处理详情
$introduce_str = '';
foreach ($content as $value) {
    $type = strval($value['type']);
    $val = trim($value['content']);
    if (empty($val)) {
        continue;
    }
    switch ($type) {
        case '1' :
        case 'text':
            $text = '<p>' . str_replace(array('\r\n', '\n', '\n\r', "\r\n", "\n", "\n\r"), '<br>', $val) . '</p>';
            break;
        case '2':
        case 'image':
            $val = yueyue_resize_act_img_url($val);
            $text = '<img src="' . $val . '"/>';
            break;
        default:
            continue;
    }
    $introduce_str .= $text;
}
if (empty($introduce_str)) {
    $result = array('result' => -1, 'message' => '图文详情为空');
    $options['data'] = $result;
    return $cp->output($options);
}
// 解析数据
$is_fill = true;
$un_check = array('c058f544c737782deacefa532d9add4c', 'location_id',
    'fc490ca45c00b1249bbe3554a4fdf6fb',);
if (version_compare($version, '1.3', '>')) {
    $un_check = array_merge($un_check, array(
        'f5f8590cd58a54e94377e6ae2eded4d9', '9431c87f273e507e6040fcb07dcb4509',
        '9fc3d7152ba9336a670e36d0ed79bc43', '47d1e990583c9c67424d369f3414728e'
    ));
}
foreach ($common as $val) {
    $key = $val['id'];
    $value = trim($val['value']);
    $option_value = $val['option_value'];
    $sys_val = empty($option_value) ? $value : $option_value;
    if (empty($sys_val) && !in_array($key, $un_check)) {
        // 备注选填
        $is_fill = false;
        break;
    }
    if (strpos($option_value, '-') !== false) {
        // 二级选项
        list($first, $second) = explode('-', $option_value);
        $system_data[$key] = trim($first);
        $second = trim($second, ',');
        if ($first != $second) {
            $system_data[$first] = $second;
//            if (strpos($second, ',') !== false) {
//                $second_arr = explode(',', $second);
//                foreach ($second_arr as $second_val) {
//                    $system_data[$first][] = $second_val;
//                }
//            } else {
//                $system_data[$first] = $second;
//            }
        }
        continue;
    }
    $system_data[$key] = $sys_val;
}
if ($is_fill === false) {
    $result = array('result' => 0, 'message' => '数据不完整');
    $options['data'] = $result;
    return $cp->output($options);
}
foreach ($choice as $val) {
    $key = $val['id'];
    $value = $val['value'];
    $option_value = $val['option_value'];
    $system_data[$key] = empty($option_value) ? $value : $option_value;
}
// 处理 培训时间 数据
$lesson_date = $system_data['e7b24b112a44fdd9ee93bdf998c6ca0e,52720e003547c70561bf5e03b95aa99f']; // 培训时间
if (!empty($lesson_date)) {
    list($system_data['e7b24b112a44fdd9ee93bdf998c6ca0e'], $system_data['52720e003547c70561bf5e03b95aa99f']) = explode('-', $lesson_date);
}
unset($system_data['e7b24b112a44fdd9ee93bdf998c6ca0e,52720e003547c70561bf5e03b95aa99f']);
if (version_compare($version, '1.3', '>')) {
    // 处理招生范围
    // 线上授课 => 全国
    if ($system_data['44f683a84163b3523afe57c2e008bc8c'] == '03afdbd66e7929b125f8597834fa83a4') {
        $system_data['location_id'] = 0;
    }
}
// 处理 default_data
$default_key = array('titles', 'stock_num', 'prices', 'location_id');
foreach ($default_key as $val) {
    $default_data[$val] = $system_data[$val];
    unset($system_data[$val]);
}
// 基本属性
$default_data['content'] = $introduce_str; // 详情

// 获取 商铺ID
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $mall_obj->get_seller_info($user_id, 2);
$store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
$type_id = 5; // 培训
// 组装数据
$op_data = array(
    'store_id' => $store_id,
    'type_id' => $type_id,
    'default_data' => $default_data,
    'img' => $imgs,
    'system_data' => $system_data,
//    'prices_de' => $prices_de,
);

if ($location_id == 'test') {
    $options['data'] = array(
        '$op_data' => $op_data,
        'param' => $client_data['data']['param'],
    );
    return $cp->output($options);
}
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
if (empty($goods_id)) {
    //插入数据
    $res = $pai_mall_goods_obj->user_add_goods($op_data, $user_id);
} else {
    //更新数据
    $res = $pai_mall_goods_obj->user_update_goods($goods_id, $op_data, $user_id);
}
if (empty($res)) {
    $result = array('result' => 0, 'message' => '数据保存失败', 'op_data' => $op_data);
    $options['data'] = $result;
    return $cp->output($options);
}
if ($res['result'] > 0) {
    $res['result'] = 1;
    $res['return_id'] = $res['result'];
}
$options['data'] = $res;
return $cp->output($options);