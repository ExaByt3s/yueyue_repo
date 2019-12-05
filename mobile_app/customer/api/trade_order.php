<?php

/**
 * 我的 评价
 * 
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];  // 商品ID
$type_id = $client_data['data']['param']['type_id'];  // 规格ID
$num = $client_data['data']['param']['num'];  // 数量
if (empty($goods_id) || $num < 1) {
    $option['data'] = array('result' => '-1', 'message' => '信息不完整');
    $cp->output($options);
    exit;
}
$api_obj = POCO::singleton('pai_mall_api_class');
$goods_result = $api_obj->api_get_goods_info_by_goods_id($goods_id);  // 获取商品详情
if ($location_id == 'test') { //for debug
    $options['data'] = $goods_result;
    $cp->output($options);
    exit;
}
if ($goods_result['result'] != 1) {
    $cp->output(array('data' => array('result' => '-2', 'message' => '无此商品')));
    exit;
}
$goods_type = array();
foreach ($goods_result['prices_data_list'] as $value) {
    $goods_type[$value['id']] = $value;
}
// 判断 该商品是否 有此规格
if (!isset($goods_type[$type_id])) {
    $cp->output(array('data' => array('result' => '-3', 'message' => '无此规格')));
    exit;
}

$detail_list = array(
    'type_id' => $type_id,
    'goods_id' => $goods_id,
    'goods_name' => $goods_result['goods_data']['titles'],
    'prices_type_id' => '',
    'prices_spec' => $goods_type['name'], // 规格
    'goods_version' => $goods_result['goods_data']['version'],
    'service_time' => 0,
    'service_location_id' => $goods_result['goods_data']['location_id'],
    'service_address' => '',
    'prices' => '￥'.$goods_type['value'],
    'quantity' => $num,
);
//$more_info = array('referer' => '', 'description' => '');

$order_result = $api_obj->api_submit_order($user_id, $detail_list, $more_info = array());
//array('result'=>0, 'message'=>'', 'order_sn'=>'');

$options['data'] = $order_result;
$cp->output($options);
