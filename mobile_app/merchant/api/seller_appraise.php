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
$page = intval($client_data['data']['param']['page']);  // 第几页
$rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)
$limit = trim($client_data['data']['param']['limit']);  // 传值如: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ( $rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    $limit_str = $limit;
}

$api_obj = POCO::singleton('pai_mall_api_class');
// 获取评论列表
$where = empty($goods_id) ? '' : 'goods_id=' . intval($goods_id);
$comment_list = $api_obj->api_packing_comment_list($user_id, false, $where, 'comment_id DESC', $limit_str, '*');

if ($location_id == 'test') {
    $options['data']['list'] = $comment_list;
    $cp->output($options);
    exit;
}

$options['data']['list'] = $comment_list;

$cp->output($options);
