<?php

/**
 * 交易 评价
 * 
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID

if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '交易号为空');
    $cp->output($options);
    exit;
}
$rating = $client_data['data']['param']['rating'];   // 评价等级
if (empty($rating)) {
    $options['data'] = array('result' => -1, 'message' => '评分为空');
    $cp->output($options);
    exit;
}
$goods_id = $client_data['data']['param']['goods_id'];   // 商品ID
$content = $client_data['data']['param']['content'];   // 评论内容
$is_anon = $client_data['data']['param']['is_anon'];   // 是否匿名

$api_obj = POCO::singleton('pai_mall_api_class');   // 实例化商家交易对象
$order_info = $api_obj->api_get_order_full_info($order_sn);

$data = array(
    'from_user_id' => $user_id, // 评价人用户ID
    'to_user_id' => $order_info['buyer_user_id'],
    'order_id' => $order_info['order_id'],
    'goods_id' => $goods_id,
    'overall_score' => $rating,
    'comment' => empty($content) ? '没有填写评论内容!' : $content,
    'is_anonymous' => intval($is_anon)
);

$result = $api_obj->api_add_buyer_comment($data);  // 评价买家

$options['data'] = $result;
$cp->output($options);
