<?php

/**
 * 交易 操作
 *
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 接受订单 accept,拒绝订单 refuse,签到订单 sign,关闭订单 close,删除订单 delete
$operate = $client_data['data']['param']['operate'];   // 交易类型
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID
$code_sn = $client_data['data']['param']['code_sn'];   // 签到码 ( 只在签到订单中用 )
if (empty($user_id) || empty($order_sn)) {
    $result = array('result' => -4, 'message' => '交易号为空');
    return $cp->output(array('data' => $result));
}
if ($operate == 'sign' && empty($code_sn)) {
    $result = array('result' => -4, 'message' => '签到码为空');
    return $cp->output(array('data' => $result));
}
$mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
switch ($operate) {
    case 'accept':   // 接受订单
        $result = $mall_order_obj->accept_order($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '订单接受成功';
        }
        break;
    case 'refuse':  // 拒绝订单
        $result = $mall_order_obj->refuse_order($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '订单拒绝成功';
        }
        break;
    case 'sign':  // 签到订单
        $result = $mall_order_obj->sign_order($code_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '订单签到成功';
        }
        break;
    case 'close':  // 关闭订单
        $result = $mall_order_obj->close_order_for_seller($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '订单关闭成功';
        }
        break;
    case 'delete':  // 删除订单
        $result = $mall_order_obj->del_order_for_seller($order_sn, $user_id);
        if ($result['result'] == 1) {
            $result['message'] = '订单删除成功';
        }
        break;
    default:
        $result = array('result' => -4, 'message' => '操作类型为空');
        break;
}
return $cp->output(array('data' => $result));
