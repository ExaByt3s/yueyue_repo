<?php

/**
 * 商品 操作
 * 
 * @since 2015-6-26
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// on_sell 上架  off_sell 下架
$operate = $client_data['data']['param']['operate'];   // 交易类型
$goods_id = $client_data['data']['param']['goods_id'];   // 商品ID
if (empty($user_id) || empty($goods_id)) {
    $result = array('result' => -4, 'message' => '商品ID为空');
    return $cp->output(array('data' => $result));
}
// 1 上架 2下架
$status_arr = array(
    'on_sell' => 1,
    'off_sell' => 2,
);
if (!isset($status_arr[$operate])) {
    $result = array('result' => -5, 'message' => '非法操作');
    return $cp->output(array('data' => $result));
}
$status = $status_arr[$operate];

$mall_goods_obj = POCO::singleton('pai_mall_goods_class');   // 实例化对象
$result = $mall_goods_obj->user_change_goods_show_status($goods_id, $status, $user_id);
if ($result['result'] == 1) {  // 修改成功
    $result['message'] = $operate == 'on_sell' ? '上架成功' : '下架成功';
}
return $cp->output(array('data' => $result));
