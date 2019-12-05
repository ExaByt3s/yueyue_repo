<?php

/**
 * 交易 评价
 * 
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID
if (empty($user_id) || empty($order_sn)) {
    $options['data'] = array('result' => -1, 'message' => '交易号为空');
    return $cp->output($options);
}
// 总体评价分数 overall_score,商品符合分数 match_score,态度评价 manner_score,质量分数 quality_score
$overall_score = $client_data['data']['param']['overall_score'];   // 总体评价分数
$match_score = $client_data['data']['param']['match_score'];   // 商品符合分数
$manner_score = $client_data['data']['param']['manner_score'];   // 态度评价
$quality_score = $client_data['data']['param']['quality_score'];   // 质量分数
if (empty($overall_score) || empty($match_score) || empty($manner_score) || empty($quality_score)) {
    $options['data'] = array('result' => -1, 'message' => '评分为空');
    return $cp->output($options);
}
$goods_id = $client_data['data']['param']['goods_id'];   // 商品ID
$content = $client_data['data']['param']['content'];   // 评论内容
$is_anon = $client_data['data']['param']['is_anon'];   // 是否匿名

$mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
$order_info = $mall_order_obj->get_order_full_info($order_sn);

$data = array(
    'from_user_id' => $user_id, // 评价人用户ID
    'to_user_id' => $order_info['seller_user_id'],
    'order_id' => $order_info['order_id'],
    'goods_id' => $goods_id,
    'overall_score' => $overall_score, // 总体评价分数
    'match_score' => $match_score, // 商品符合分数
    'manner_score' => $manner_score, // 态度评价
    'quality_score' => $quality_score, // 质量分数
    'comment' => empty($content) ? '没有填写评论内容!' : $content,
    'is_anonymous' => intval($is_anon)
);
$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
$result = $mall_comment_obj->add_seller_comment($data);  // 评价商家

$options['data'] = $result;
return $cp->output($options);
