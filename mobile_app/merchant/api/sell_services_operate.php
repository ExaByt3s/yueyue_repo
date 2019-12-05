<?php

/**
 * 商品 操作
 * 
 * @since 2015-6-26
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// on_sell 上架  off_sell 下架
$operate = $client_data['data']['param']['operate'];   // 交易类型
$goods_id = $client_data['data']['param']['goods_id'];   // 商品ID
if (empty($user_id) || empty($goods_id)) {
    $result = array('result' => -4, 'message' => '商品ID为空');
    $cp->output(array('data' => $result));
    exit;
}
// 1 上架 2下架
$status_arr = array(
    'on_sell' => 1,
    'off_sell' => 2,
);
if (!isset($status_arr[$operate])) {
    $result = array('result' => -5, 'message' => '非法操作');
    $cp->output(array('data' => $result));
    exit;
}
$status = $status_arr[$operate];

$api_obj = POCO::singleton('pai_mall_api_class');   // 实例化对象
$result = $api_obj->api_user_change_goods_show_status($goods_id, $status, $user_id);
$cp->output(array('data' => $result));
