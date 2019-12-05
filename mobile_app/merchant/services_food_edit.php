<?php

/**
 * 美食达人(提交)接口
 *
 * @author willike
 * @since 2015/10/15
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$options['data'] = array('result' => 0, 'message' => '暂不支持编辑');
return $cp->output($options);

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = intval($client_data['data']['param']['goods_id']);   // 商品ID
$common = $client_data['data']['param']['common'];
$standerd = $client_data['data']['param']['standerd'];  // 价格
$cover = $client_data['data']['param']['cover'];  // 背景图
$content = $client_data['data']['param']['content'];  // 图文详情
$guide = $client_data['data']['param']['guide'];  // 导航图片
if (empty($user_id) || empty($common)) {
    $result = array('result' => 0, 'message' => '数据未完整!');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($content)) {
    $result = array('result' => -1, 'message' => '图文详情为空');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($standerd)) {
    $result = array('result' => -1, 'message' => '规格为空');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($guide)) {
    $result = array('result' => -3, 'message' => '导航图为空');
    $options['data'] = $result;
    return $cp->output($options);
}
$imgs = array();
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
// 解析(基础)数据
$is_fill = true;
foreach ($common as $val) {
    $key = $val['id'];
    $value = trim($val['value']);
    $option_value = $val['option_value'];
    $sys_val = empty($option_value) ? $value : $option_value;
    if (empty($sys_val)) {
        $is_fill = false;
        break;
    }
    $system_data[$key] = $sys_val;
}
if ($is_fill === false) {
    $result = array('result' => 0, 'message' => '数据不完整');
    $options['data'] = $result;
    return $cp->output($options);
}
// 导航图
$system_data['e56954b4f6347e897f954495eab16a88'] = $guide;
// 处理 default_data
$default_key = array('titles', 'location_id');
foreach ($default_key as $val) {
    $default_data[$val] = $system_data[$val];
    unset($system_data[$val]);
}
// 基本属性
$default_data['content'] = $introduce_str; // 详情
// 价格
$temp_i = 0;
foreach ($standerd as $value) {
    $key = $value['id'];
    if (empty($key)) {
        // 没有 id 时,表示新增, 生成一个ID
        $key = time() . $temp_i;  // (9+1=10 位数)
        $temp_i++;
    }
    $options = $value['options'];
    $o_val = array();
    foreach ($options as $v) {
        $o_val[$v['id']] = $v['value'];
    }
    $share_num = intval($o_val['share_num']);
    $prices = $o_val['price'];
    if (empty($prices) || !is_numeric($prices) || $prices <= 0 || $share_num < 1) {
        // 价格为空, 价格非数字, 就餐人数小于1
        continue;
    }
    $stock_num = intval($o_val['stock_num']);
    $prices_diy[$key] = array(
        'name_v1' => $o_val['name'],
        'name_v2' => $share_num,
        'name' => $o_val['name'] . '（' . $share_num . '人）',
        'prices' => $prices,
        'stock_num' => $stock_num,
    );
}
if (empty($prices_diy)) {
    $result = array('result' => -2, 'message' => '套餐未填写完整');
    $options['data'] = $result;
    return $cp->output($options);
}
// 获取 商铺ID
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $mall_obj->get_seller_info($user_id, 2);
$store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
$type_id = 41; // 美食
// 组装数据
$op_data = array(
    'store_id' => $store_id,
    'type_id' => $type_id,
    'default_data' => $default_data,
    'img' => $imgs,
    'system_data' => $system_data,
    'prices_diy' => $prices_diy,
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
