<?php

/**
 * 我的首页
 * 
 * @author heyaohua
 * @editor chenweibiao<chenwb@yueus.com>
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../../protocol_common.inc.php");

$cp = new poco_communication_protocol_class();
$client_data = $cp->get_input(array('be_check_token' => false));

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];
$ret = $cp->check_access_token_is_matching($user_id, $access_token);
if (!$ret) {
    $cp->output(array('data' => array()));
    exit;
}
$obj = POCO::singleton('pai_user_class');
$user_info = $obj->get_user_info_by_user_id($user_id);  // 获取用户 信息
if (empty($user_info)) {
    $cp->output(array('data' => array()));
    exit;
}
$credit_url = 'http://yp.yueus.com/mobile/app?from_app=1#mine/authentication_list';
$setup_url = 'http://yp.yueus.com/mobile/app?from_app=1#account/setup';
$bill_url = 'http://yp.yueus.com/mobile/m2/mine/bill/?type=trade';
$pw_url = 'http://yp.yueus.com/mobile/app?from_app=1#account/setup/bind/enter_pw/form_setup';
$event_url = 'http://yp.yueus.com/mobile/app?from_app=1#mine/status';
$alipay_url = 'http://yp.yueus.com/mobile/m2/mine/bind_alipay/';
$coupon_url = 'http://yp.yueus.com/mobile/app?from_app=1#coupon/list/available';
$recharge_url = 'http://yp.yueus.com/mobile/m2/recharge/index.php';
$index_info = array(
    'mid' => '122LB08002',
    'user_id' => $user_id,
    'nickname' => get_user_nickname_by_user_id($user_id),
    'icon' => get_user_icon($user_id, 86, TRUE),
    'location_id' => $user_info['location_id'],
    'city_name' => $user_info['city_name'], // 所在城市
    'user_level' => $user_info['user_level'], // 用户认证等级
    // 一些URL
    'home_url' => 'http://yp.yueus.com/mobile/app?from_app=1#zone/' . $user_id . '/cameraman',
    'home_url_wifi' => 'http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/' . $user_id . '/cameraman',
    'code_url' => 'yueyue://goto?type=inner_app&pid=1220092',
    'recharge_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($recharge_url) . '&wifi_url=' . urlencode($recharge_url) . '&showtitle=1',
    'credit_url' => "yueyue://goto?type=inner_web&url=" . urlencode($credit_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $credit_url)),
    'yuepai_url' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220120&type=inner_app',
    'setup_url' => "yueyue://goto?type=inner_web&url=" . urlencode($setup_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $setup_url)),
    'bill_url' => "yueyue://goto?type=inner_web&url=" . urlencode($bill_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $bill_url)) . '&showtitle=2',
    'pw_url' => "yueyue://goto?type=inner_web&url=" . urlencode($pw_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $pw_url)),
    'alipay_url' => "yueyue://goto?type=inner_web&url=" . urlencode($alipay_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $alipay_url)) . '&showtitle=2',
    'event_url' => "yueyue://goto?type=inner_web&url=" . urlencode($event_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $event_url)),
    'coupon_url' => "yueyue://goto?type=inner_web&url=" . urlencode($coupon_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $coupon_url)),
    // 一些 统计
    'waipai_num' => strval(count_waipai_order_num($user_id)),
    'yuepai_num' => strval(count_yuepai_order_num($user_id)),
    'code_num' => strval(count_act_ticket($user_id)),
    // 分享
    'share' => array(),
);

// 优惠券数量
$coupon_obj = POCO::singleton('pai_coupon_class');
$coupon_num = $coupon_obj->get_user_coupon_list_by_tab('available', $user_id, true);
$index_info['coupon_num'] = strval($coupon_num);

// 用户分享
$cameraman_card_obj = POCO::singleton('pai_cameraman_card_class');
$share_text = $cameraman_card_obj->get_share_text($user_id);
$index_info['share'] = $share_text;

// TT 链接
$task_request_obj = POCO::singleton('pai_task_request_class');
if ($task_request_obj->get_request_is_have($user_id)) {
    $index_info['tt_url'] = "yueyue://goto?type=inner_app&pid=1220079";
} else {
    $index_info['tt_url'] = "yueyue://goto?type=inner_app&pid=1220080";
}

$options['data'] = $index_info;
$cp->output($options);
