<?php

/**
 * 服务商品编辑(提交)接口
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品ID
$post_data = $client_data['data']['param']['post_json_data'];  // 提交的数据
if (empty($user_id) || empty($post_data)) {
    $result = array('result' => 0, 'message' => '没有该用户');
    $options['data'] = $result;
    return $cp->output($options);
}
$type_id = 31; // 模特
// 处理详情
$introduce_str = '';
$content = $post_data['content'];
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
if (empty($post_data['titles'])) {
    $result = array('result' => -1, 'message' => '标题为空');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($introduce_str)) {
    $result = array('result' => -1, 'message' => '图文详情为空');
    $options['data'] = $result;
    return $cp->output($options);
}
// 基本属性
$default_data = array(
    'titles' => $post_data['titles'],
    'content' => $introduce_str, // 详情
);
// 轮播图片
$img = array();
foreach ($post_data['img_list'] as $value) {
    $img_val = $value['img'];
    if (empty($img_val) || !filter_var($img_val, FILTER_VALIDATE_URL)) {
        continue;
    }
    $img_val = yueyue_resize_act_img_url($img_val);  // 转为原图保存
    $img[] = array('img_url' => $img_val);
}
// 价格
$special_ids = array();
foreach ($post_data['prices_list'] as $value) {
    $id = $value['id'];
    if (!is_numeric($id)) {
        // 非数字 , 例如: 淘宝的 起拍件数key
        $special_ids[$id] = $value;
        continue;
    }
    $value = floatval($value['value']); // 价格
    if ($value == '' || $value <= 0 || $value == '0.0') {
        continue;
    }
    $prices_de[$id] = sprintf('%.2f', $value);
}
if (empty($prices_de)) {
    $result = array('result' => -2, 'message' => '没有填写价格');
    $options['data'] = $result;
    return $cp->output($options);
}
// 特有属性
$style = $post_data['style'];
if ($style == '72b32a1f754ba1c09b3695e0cb6cde7f') {
    // 淘宝 没有限制人数, 有 起拍件数
    $system_data = array(
        'd9d4f495e875a2e075a1a4a6e1b9770f' => $style, // 风格
        '16a5cdae362b8d27a1d8f8c7b78b4330' => intval($special_ids['16a5cdae362b8d27a1d8f8c7b78b4330']), // 起拍件数
    );
} else {
    $system_data = array(
        'd9d4f495e875a2e075a1a4a6e1b9770f' => $style, // 风格
        '66f041e16a60928b05a7e228a89c3799' => intval($post_data['limit_num']), // 限制人数
    );
}
// 获取 商铺ID
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $mall_obj->get_seller_info($user_id, 2);
$store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
// 组装数据
$op_data = array(
    'store_id' => $store_id,
    'type_id' => $type_id,
    'default_data' => $default_data,
    'img' => $img,
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
    $res['goods_id'] = $res['result'];
}
$options['data'] = $res;
return $cp->output($options);
