<?php

/**
 * 收藏 商家/商品 操作
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-8-26
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$target_id = $client_data['data']['param']['target_id'];  // 目标 ID  ( 商家ID/商品ID )
$target_type = $client_data['data']['param']['target_type'];  // 目标类型 ( seller/goods )
$operate = $client_data['data']['param']['operate'];  // 操作类型 ( follow/unfollow )
if (empty($user_id)){
	$result = array(
        'result' => 0,
        'message' => '请先登录',
    );
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($target_id) || empty($target_type) || empty($operate)) {
    $result = array(
        'result' => 0,
        'message' => '参数不完整',
    );
    $options['data'] = $result;
    return $cp->output($options);
}
$follow_obj = POCO::singleton('pai_mall_follow_user_class');
switch ($operate) {
    case 'follow':
        switch ($target_type) {
            case 'seller':
                $result = $follow_obj->add_user_follow($user_id, $target_id);
                break;
            case 'goods':
                $result = $follow_obj->add_goods_follow($user_id, $target_id);
                break;
            default:
                $result = array();
                break;
        }
        break;
    case 'unfollow':
        switch ($target_type) {
            case 'seller':
                $result = $follow_obj->cancel_user_follow($user_id, $target_id);
                break;
            case 'goods':
                $result = $follow_obj->cancel_goods_follow($user_id, $target_id);
                break;
            default:
                $result = array();
                break;
        }
        if ($result['result'] == 1) {
            $result['message'] = '已取消收藏';
        }
        break;
    default:
        $result = array();
        break;
}
if (isset($result['message'])) {
    $result['message'] = str_replace('关注', '收藏', $result['message']);
}
$options['data'] = $result;
return $cp->output($options);
