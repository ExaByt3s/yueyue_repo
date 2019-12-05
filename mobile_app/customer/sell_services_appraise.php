<?php

/**
 * 商品评价
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-7-8
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$seller_user_id = trim($client_data['data']['param']['seller_user_id']);  // 商家ID
$goods_id = $client_data['data']['param']['goods_id'];  // 商品ID
$page = intval($client_data['data']['param']['page']);  // 第几页
$rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)
$limit = trim($client_data['data']['param']['limit']);  // 传值如: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    $limit_str = $limit;
}

$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
// 获取 评论列表
$where = empty($goods_id) ? '' : 'goods_id=' . intval($goods_id);
if (empty($seller_user_id)) {
    // 消费者 对 商品的评价
    $comment_result = $mall_comment_obj->get_buyer_comment_list($user_id, false, $where, 'comment_id DESC', $limit_str, '*');
} else {
    // 商家 对 商品的评价
    $comment_result = $mall_comment_obj->get_seller_comment_list($seller_user_id, false, $where, 'comment_id DESC', $limit_str, '*');
}
if ($location_id == 'test') {
    $options['data'] = array(
        'param' => $client_data['data']['param'],
        '$where' => $where,
        'list' => $comment_result,
    );
    return $cp->output($options);
}
$comment_list = array();
foreach ($comment_result as $value) {
    $from_user_id = $value['from_user_id'];
    $goods_id = $value['goods_id'];
    $type_id = $value['type_id'];
    $is_anonymous = $value['is_anonymous'];  // 是否匿名评价 0为否 1为是
    //$name = $is_anonymous == 1 ? (mb_substr($name, 0, 1, 'UTF8') . '***' . mb_substr($name, -1, 1, 'UTF8')) : $name;
    $order_id = $value['order_id'];
    $title = $type_id == 20 ? '直接付款' : '暂无内容';
    if (!empty($goods_id)) {
        $goods_info = $task_goods_obj->get_goods_info_by_goods_id($goods_id);
        if ($location_id == 'test1') {
            $options['data'] = array(
                '$goods_id' => $goods_id,
                '$goods_info' => $goods_info,
            );
            $cp->output($options);
            exit;
        }
        $title = $goods_info['data']['goods_data']['titles'];
    }
    if (empty($seller_user_id)) {
        // 消费者 ( 来自商家的评价 )
        $name = $is_anonymous == 1 ? '匿名' : get_seller_nickname_by_user_id($from_user_id);  // 用户名
        $avatar = $is_anonymous == 1 ? get_seller_user_icon(10000) : get_seller_user_icon($from_user_id);
    } else {
        // 商家 ( 来自消费者的评价 )
        $name = $is_anonymous == 1 ? '匿名' : get_user_nickname_by_user_id($from_user_id);  // 用户名
        $avatar = $is_anonymous == 1 ? get_user_icon(10000) : get_user_icon($from_user_id);
    }
    $score = $value['overall_score'];
    $score = empty($score) ? 5 : $score;
    $comment_list[] = array(
        'type_id' => $type_id,
        'from_user_id' => $from_user_id,
        'avatar' => $avatar, // 头像
        'customer' => empty($name) ? '' : $name,
        'service_title' => ' ' . $title,
        'rating' => sprintf('%.1f', $score), // 评分
        'comment' => $value['comment'], // 评论内容
        'add_time' => date('Y-m-d', $value['add_time']), // 评论时间
    );
    unset($goods_info);
}

$options['data']['list'] = $comment_list;
return $cp->output($options);
