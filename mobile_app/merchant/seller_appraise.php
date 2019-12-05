<?php

/**
 * 我的 评价
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$buyer_id = intval($client_data['data']['param']['buyer_id']);  // 买家ID
$goods_id = intval($client_data['data']['param']['goods_id']);  // 商品ID
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
// 获取评论列表
if (empty($buyer_id)) {
    $where = empty($goods_id) ? '' : 'goods_id=' . $goods_id;
    $comment_result = $mall_comment_obj->get_seller_comment_list($user_id, false, $where, 'add_time DESC', $limit_str, '*');
} else {
    $comment_result = $mall_comment_obj->get_buyer_comment_list($buyer_id, false, '', 'add_time DESC', $limit_str, '*');
}
if ($location_id == 'test') {
    $options['data'] = array(
        'user_id' => $user_id,
        'where' => $where,
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
    $order_id = $value['order_id'];
    $title = $type_id == 20 ? '直接付款' : '';
    if (!empty($goods_id)) {
        $goods_info = $task_goods_obj->user_get_goods_info($goods_id, $user_id);
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
    $score = $value['overall_score'];  // 评分
    $score = empty($score) ? 5 : $score;
    if (empty($buyer_id)) {
        $name = $is_anonymous == 1 ? '匿名' : get_user_nickname_by_user_id($from_user_id);  // 用户名
        // 商家 ( 来自消费者的评价 )
        $avatar = $is_anonymous == 1 ? get_user_icon(10000) : get_user_icon($from_user_id);
    } else {
        $name = $is_anonymous == 1 ? '匿名' : get_seller_nickname_by_user_id($from_user_id);  // 商家名
        // 消费者 ( 来自商家的评价 )
        $avatar = $is_anonymous == 1 ? get_seller_user_icon(10000) : get_seller_user_icon($from_user_id);
    }
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
