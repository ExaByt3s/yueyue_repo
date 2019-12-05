<?php

/**
 * 交易 评价
 *
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID
if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '交易号为空');
    return $cp->output($options);
}
$rating = intval($client_data['data']['param']['rating']);   // 评价等级
if (empty($rating)) {
    $options['data'] = array('result' => -1, 'message' => '评分为空');
    return $cp->output($options);
}
$goods_id = $client_data['data']['param']['goods_id'];   // 商品ID
if (empty($goods_id)) {
    $options['data'] = array('result' => -1, 'message' => '商品ID为空');
    return $cp->output($options);
}
$content = $client_data['data']['param']['content'];   // 评论内容
$is_anon = $client_data['data']['param']['is_anon'];   // 是否匿名

$mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
$order_info = $mall_order_obj->get_order_full_info($order_sn);

$data = array(
    'from_user_id' => $user_id, // 评价人用户ID
    'to_user_id' => $order_info['buyer_user_id'],
    'order_id' => $order_info['order_id'],
    'goods_id' => $goods_id,
    'overall_score' => $rating,
    'comment' => empty($content) ? '没有填写评论内容!' : $content,
    'is_anonymous' => intval($is_anon)
);
$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
$result = $mall_comment_obj->add_buyer_comment($data);  // 评价买家

$options['data'] = $result;
return $cp->output($options);