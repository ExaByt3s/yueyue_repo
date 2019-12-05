<?php

/**
 * 交易 操作
 * 
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 关闭订单 close,删除订单 delete
$operate = $client_data['data']['param']['operate'];   // 交易类型
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID
if (empty($user_id) || empty($order_sn)) {
    $result = array('result' => -4, 'message' => '交易号为空');
    $cp->output(array('data' => $result));
    exit;
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
switch ($operate) {
    case 'accept':   // 接受订单
        break;
    case 'refuse':  // 拒绝订单
        break;
    case 'sign':  // 签到订单
        break;
    case 'close':  // 关闭订单
        $result = $mall_order_obj->close_order_for_buyer($order_sn, $user_id);
        break;
    case 'delete':  // 删除订单
        $result = $mall_order_obj->del_order_for_buyer($order_sn, $user_id);
        break;
    default:
        $result = array('result' => -4, 'message' => '操作类型为空');
        break;
}
$cp->output(array('data' => $result));
