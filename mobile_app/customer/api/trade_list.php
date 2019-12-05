<?php

/**
 * 交易 列表
 * 
 * @since 2015-7-1
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID
// 全部 all 待付款 unpaid 待确认 tbc 待签到 checkin 已完成  completed 已关闭 closed
$trade_type = $client_data['data']['param']['trade_type'];   // 交易类型
// 影棚租赁 studio_rent ,生活服务 life_service,化妆服务 makeup,摄影培训 shooting_training,模特服务 model_service,摄影服务 photo_service 
$goods_type = $client_data['data']['param']['goods_type'];   // 商品类型
if (empty($user_id)) {
    $cp->output(array('data' => array()));
    exit;
}
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
// 待付款 0, 待确认 1, 待签到 2, 已关闭 7, 已完成 8, 所有 -1 
switch ($trade_type) {
    case 'unpaid':  // 待付款
        $status = 0;
        break;
    case 'tbc':  // 待确认
        $status = 1;
        break;
    case 'checkin':  // 待签到
        $status = 2;
        break;
    case 'completed':  // 已完成
        $status = 8;
        break;
    case 'closed':  // 已关闭
        $status = 7;
        break;
    default:  // 所有
        $status = -1;
        break;
}
$goods_arr = array('studio_rent' => 12, 'life_service' => 2, 'makeup' => 3, 'shooting_training' => 5, 'model_service' => 31, 'photo_service' => 40);
$type_id = isset($goods_arr[$goods_type]) ? $goods_arr[$goods_type] : 0; // 某个品类/全部

$api_obj = POCO::singleton('pai_mall_api_class');   // 实例化商家交易对象
$trade_data = $api_obj->api_get_order_list_for_buyer($user_id, $type_id, $status, false, 'order_id DESC', $limit_str, '*');
if ($location_id == 'test') {
    $options['data']['list'] = $trade_data;  // for test 
    $cp->output($options);
    exit;
}
$trade_list = array();
foreach ($trade_data as $value) {
    $order_sn = $value['order_sn'];
    $status = $value['status'];
    $is_buyer_comment = $value['is_buyer_comment'];  // 是否评价
    $status_str = $value['status_str'];
    if ($status == 8 && $is_buyer_comment == 0) {  // 未评价
        $status = 10;
        $status_str = '待评价';
    }
    $seller_user_id = $value['seller_user_id'];
    $trade_info = array(
        'order_sn' => $order_sn,
        'type_id' => $value['type_id'], // 商品品类
//        'type_name' => $type_arr[$value['type_id']]['name'],
        'title' => get_user_nickname_by_user_id($seller_user_id), // 标题, 可能是卖家
//        'title' => $value['detail_list'][0]['goods_name'], // 标题, 可能是消费者
        'desc' => $value['detail_list'][0]['goods_name'], // 描述, 可能是商品名
        'link' => 'yueyue://goto?user_id=' . $user_id . '&order_sn=' . $order_sn . '&pid=1220106&type=inner_app', // 跳转订单详情
        'status' => $status, // 状态
        'status_str' => $status_str, // 状态名称
        'cost' => $value['total_amount'], // 金额
        'goods_id' => $value['detail_list'][0]['goods_id'], // 商品ID
        'thumb' => $value['detail_list'][0]['goods_images'], // 预览图
        'action' => btn_action($value['status'], $is_buyer_comment),
    );
    $trade_list[] = $trade_info;
}
$options['data']['list'] = $trade_list;
$cp->output($options);

/**
 * 按钮 操作
 * 
 * @param string $status 交易状态
 * @param string $is_buyer_comment 买家是否评论
 * @return array 
 */
function btn_action($status, $is_buyer_comment) {
    if ($is_buyer_comment == 1) {  // 买家已评价
        return array();
    }
    // 按钮文案
    $action_arr = array(
        0 => '付款.pay|取消订单.close',
//        1 => '拒绝.refuse|同意.accept',
        2 => '申请退款.close|出示二维码.sign',
        7 => '删除订单.delete',
        8 => '评价对方.appraise'
    );
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}
