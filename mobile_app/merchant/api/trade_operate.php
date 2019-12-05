<?php

/**
 * 交易 操作
 * 
 * @since 2015-6-18
 * @author chenweibiao <chenwb@yueus.com>
 */
//临时，支付系统测试平台
define('G_PAI_ECPAY_DEV', 1);   // for test

include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 接受订单 accept,拒绝订单 refuse,签到订单 sign,关闭订单 close,删除订单 delete
$operate = $client_data['data']['param']['operate'];   // 交易类型
$order_sn = $client_data['data']['param']['order_sn'];   // 交易ID
$code_sn = $client_data['data']['param']['code_sn'];   // 签到码 ( 只在签到订单中用 )
if (empty($user_id) || empty($order_sn)) {
    $result = array('result' => -4, 'message' => '交易号为空');
    $cp->output(array('data' => $result));
    exit;
}
if ($operate == 'sign' && empty($code_sn)) {
    $result = array('result' => -4, 'message' => '签到码为空');
    $cp->output(array('data' => $result));
    exit;
}
$api_obj = POCO::singleton('pai_mall_api_class');   // 实例化商家交易对象
switch ($operate) {
    case 'accept':   // 接受订单
        $result = $api_obj->api_accept_order($order_sn, $user_id);
        if($result['result'] == 1){
            $result['message'] = '订单接受成功';
        }
        break;
    case 'refuse':  // 拒绝订单
        $result = $api_obj->api_refuse_order($order_sn, $user_id);
        break;
    case 'sign':  // 签到订单
        $result = $api_obj->api_sign_order($code_sn, $user_id);
        break;
    case 'close':  // 关闭订单
        $result = $api_obj->api_close_order_for_seller($order_sn, $user_id);
        break;
    case 'delete':  // 删除订单
        $result = $api_obj->api_del_order_for_seller($order_sn, $user_id);
        break;
    default:
        $result = array('result' => -4, 'message' => '操作类型为空');
        break;
}
$cp->output(array('data' => $result));
