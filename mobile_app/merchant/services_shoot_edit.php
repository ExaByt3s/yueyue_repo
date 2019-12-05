<?php

/**
 * 摄影服务编辑(提交)接口
 * @author willike <chenwb@yueus.com>
 * @since 2015/10/15
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = intval($client_data['data']['param']['goods_id']);   // 商品ID
$common = $client_data['data']['param']['common'];
$package = $client_data['data']['param']['package'];
$cover = $client_data['data']['param']['cover'];  // 背景图
$showcase = $client_data['data']['param']['showcase'];  // 作品展示
if (empty($user_id) || empty($common) || empty($package) || empty($cover)) {
    $result = array('result' => 0, 'message' => '数据未完整');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($showcase) || count($showcase) < 5) {
    $result = array('result' => -1, 'message' => '作品图片少于5张');
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
foreach ($showcase as $value) {
    if (empty($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
        continue;
    }
    $val = yueyue_resize_act_img_url($value);  // 获取原图
    $text = '<img src="' . $val . '"/>';
    $introduce_str .= $text;
}
if (empty($introduce_str)) {
    $result = array('result' => -1, 'message' => '作品图为空');
    $options['data'] = $result;
    return $cp->output($options);
}
// 解析(基础)数据
$is_fill = true;
foreach ($common as $val) {
    $key = $val['id'];
    $value = trim($val['value']);
    $option_value = $val['option_value'];
    $option_value_arr = explode(',', $option_value);
    if (count($option_value_arr) > 1) {
        $option_value = $option_value_arr;
    }
    $sys_val = empty($option_value) ? $value : $option_value;
    if (empty($sys_val) && !in_array($key, array('location_id'))) {
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
//            $second_arr = explode(',',$second);
//            foreach($second_arr as $second_val){
//                $system_data[$first] = $second_val;
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
// 拍摄套图
$system_data['c3e878e27f52e2a57ace4d9a76fd9acf'] = $showcase;
// 处理 default_data
$default_key = array('titles', 'location_id');
foreach ($default_key as $val) {
    $default_data[$val] = $system_data[$val];
    unset($system_data[$val]);
}
// 基本属性
$default_data['content'] = $introduce_str; // 详情
// 解析 套餐
$prices_de = array();
foreach ($package as $val) {
    $key = $val['id'];
    $options = $val['options'];
    $pk = array();
    $is_fill = true;
    foreach ($options as $v) {
        $id = $v['id'];
        $o_val = $v['value'];
        if (strpos($id, 'prices_de') !== false) {
            // 价格
            if (empty($o_val) || !is_numeric($o_val)) {
                // 价格为空, 或非数字
                $is_fill = false;
                continue;
            }
            list($name, $o_key) = explode('.', $id);
            $prices_de[$o_key] = $o_val;
            continue;
        }
        if ($is_fill == false) {
            // 有价格, 才算是一个套餐
            $pk = array();
            break;
        }
        $pk[$id] = $o_val;
    }
    if (!empty($pk)) {
        $system_data[$key] = $pk;  // 套餐
    }
}
if (empty($prices_de)) {
    $result = array('result' => -3, 'message' => '套餐为空');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($prices_de['311'])) {
    $result = array('result' => -3, 'message' => '标配套餐为空');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($prices_de['310'])) {
    $result = array('result' => -3, 'message' => '创意套餐为空');
    $options['data'] = $result;
    return $cp->output($options);
}
// 获取 商铺ID
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $mall_obj->get_seller_info($user_id, 2);
$store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
$type_id = 40; // 摄影服务
// 组装数据
$op_data = array(
    'store_id' => $store_id,
    'type_id' => $type_id,
    'default_data' => $default_data,
    'img' => $imgs,
    'system_data' => $system_data,
    'prices_de' => $prices_de,
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