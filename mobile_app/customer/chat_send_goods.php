<?php

/**
 * 聊天窗口 发送商品
 * 
 * @author heyaohua
 * @editor willike <chenwb@yueus.com>
 * @since 2015/7/23
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$send_user_id = $client_data['data']['param']['user_id'];
$send_user_role = $client_data['data']['param']['send_user_role'];
$to_user_id = $client_data['data']['param']['to_user_id'];
$to_user_role = $client_data['data']['param']['to_user_role'];
$goods_id = $client_data['data']['param']['goods_id'];

if (empty($send_user_role)) {
    $send_user_role = 'yuebuyer';
}
if (empty($to_user_role)) {
    $to_user_role = 'yueseller';
}

if (empty($goods_id) || empty($to_user_id)) {
    $options['data'] = array(
        'result' => 0,
        'message' => '信息不完整',
    );
    return $cp->output($options);
}
// 获取商品信息
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_result = $task_goods_obj->get_goods_info_by_goods_id($goods_id);
if ($location_id == 'test1') {
    $options['data'] = $goods_result;
    return $cp->output($options);
}
if ($goods_result['result'] != 1) {
    $options['data'] = array(
        'result' => -1,
        'message' => '该商品已下架',
    );
    return $cp->output($options);
}
$goods = $goods_result['data'];
$goods_user_id = $goods['goods_data']['user_id'];  // 商品卖家
$goods_type_id = intval($goods['goods_data']['type_id']);  // 商品分类
if ($goods_user_id != $to_user_id) {
    $options['data'] = array(
        'result' => -2,
        'message' => '非当前卖家商品',
    );
    return $cp->output($options);
}
$title = $goods['goods_data']['titles'];  // 商品标题
$prices = $goods['goods_data']['prices'];  // 商品价格
$prices = sprintf('%.2f', $prices); // 保留两位小数
$images = $goods['goods_data']['images'];  // 商品图片
$images = yueyue_resize_act_img_url($images, 260);

// android 1.0.0  ios 1.0.1
$client = new GearmanClient();
$client->addServer('172.18.5.211', 9830);
$client->setTimeout(5000); // 设置超时
$send_json = array(
    'pocoid' => 'yuebuyer/' . $send_user_id, // 消费者
);
$send_user_version = $client->do('get_client_info', json_encode($send_json)); // 获取版本号
$send_version_arr = json_decode(trim($send_user_version), TRUE);
$send_card_style = 2;
if (!empty($send_version_arr['appver']) && version_compare($send_version_arr['appver'], '3.1.0') >= 0) {
    // 消费者版 > 3.0.10 出卡片3类型
    $send_card_style = 4;
}
$to_json = array(
    'pocoid' => 'yueseller/' . $to_user_id, // 商家
);
$to_user_version = $client->do('get_client_info', json_encode($to_json)); // 获取版本号
$to_version_arr = json_decode(trim($to_user_version), TRUE);
$to_card_style = 2;
if (!empty($to_version_arr['appver']) && version_compare($to_version_arr['appver'], '1.1.0') >= 0) {
    // 商家版 > 1.0.1 出卡片3类型
    $to_card_style = 4;
}
$type_data = array(
    2 => array(
        'media_type' => 'card',
        'card_style' => '2',
        'card_text1' => $title . "\r\n" . '￥' .$prices,
        'card_title' => '查看服务详情',
    ),
    3 => array(
        'media_type' => 'card',
        'card_style' => '3',
        'card_text1' => $title,
        'file_small_url' => $images,
        'card_text2' => '￥' . $prices,
    ),
    4 => array(
        'media_type' => 'merchandise',
        'card_style' => '3',
        'card_text1' => $title,
        'file_small_url' => $images,
        'card_text2' => '￥' . $prices,
    ),
);

$to_send_data = isset($type_data[$send_card_style]) ? $type_data[$send_card_style] : $type_data[2];  // 发送者
$send_data = isset($type_data[$to_card_style]) ? $type_data[$to_card_style] : $type_data[2];  // 接收者

if ($location_id == 'test') {
    $options['data'] = array(
        'version_arr' => $version_arr,
        'param' => $client_data['data']['param'],
        'send_data' => $send_data,
        'to_send_data' => $to_send_data,
    );
    return $cp->output($options);
}
$pai_send_obj = POCO::singleton('pai_information_push');
$pai_send_obj->card_send_for_goods($send_user_id, $send_user_role, $to_user_id, $to_user_role, $send_data, $to_send_data, $goods_id, $goods_type_id);

$options['data'] = array(
    'result' => 1,
    'message' => '发送成功',
);
return $cp->output($options);

