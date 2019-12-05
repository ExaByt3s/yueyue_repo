<?php

/**
 * 服务商品页
 * 
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define('ORDER_LINK_HEAD', 'http://yp.yueus.com/mall/user/test');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号

$api_obj = POCO::singleton('pai_mall_api_class');
$goods_info = $api_obj->api_packing_get_goods_info_by_goods_id($goods_id);

if( ! empty($goods_info) )
{
    
    $goods_info['user']['homepage'] = array(
        'title'=>'个人主页',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_info['user']['user_id'] . '&pid=1220103&type=inner_app',
    );
    $goods_info['business']['totaltrade'] = array(
        'title' => '交易次数',
        'value' => $goods_info['business']['value'], // $goods['goods_data']['review_times'] 评价次数
    );
    $goods_info['business']['request'] = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $goods_info['user']['user_id'] . '&goods_id=' . $goods_info['business']['goods_id'] . '&pid=1220075&type=inner_app'; // 商品评价
    
    // 按钮
    $goods_info['action'] = array(
        array(
            'title' => '立即下单',
            'request' => 'yueyue://goto?type=inner_web&url=' . $goods_info['order_url'] . '&wifi_url=' . $goods_info['order_url'] . '&showtitle=1',
        ),
        array(
            'title' => '咨询',
//            'request' => 'consult',
            'request' => 'yueyue://goto?user_id=' . $user_id . '&receiver_id=' . $goods_info['user']['user_id'] . '&receiver_name=' . $goods_info['user_name'] . '&goods_id=' . $goods_id . '&pid=1220021&type=inner_app',
        ),
    );
    
    // android 需求 2015-7-8
    $goods_info['order_link'] = 'yueyue://goto?type=inner_web&url=' . $goods_info['order_url'] . '&wifi_url=' . $goods_info['order_url'] . '&showtitle=1';
    $goods_info['consult_link'] = 'yueyue://goto?user_id=' . $user_id . '&receiver_id=' . $goods_info['user']['user_id'] . '&receiver_name=' . $goods_info['user_name'] . '&goods_id=' . $goods_id . '&pid=1220021&type=inner_app';
    
    // 获取用户分享信息
    $model_card_obj = POCO::singleton('pai_model_card_class');
    $share_result = $model_card_obj->get_share_text($user_id);
    $goods_info['share'] = $share_result;
}
        

if ($location_id == 'test') { //for debug
    $options['data'] = $api_obj->api_get_goods_info_by_goods_id($goods_id);
    $cp->output($options);
    exit;
}


if(empty($goods_info))
{
    $cp->output(array('data' => array()));
    exit;
}

$options['data'] = $goods_info; 
$cp->output($options);
