<?php
/**
 * 我的钱包
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015/11/5
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$location_id = $client_data['data']['param']['location_id'];
$obj = POCO::singleton('pai_user_class');
$user_info = $obj->get_user_info($user_id);  // 获取用户 信息
if (empty($user_info)) {
    return $cp->output(array('data' => array()));
}

$coupon_url = 'http://yp.yueus.com/mall/user/coupon/list.php';   // 优惠券
$recharge_url = 'http://yp.yueus.com/mobile/m2/recharge/index.php';  // 充值
$withdraw_url = '';  // 提现
$bill_url = 'http://yp.yueus.com/mobile/m2/mine/bill/?type=trade';  // 账单
$alipay_url = 'http://yp.yueus.com/mobile/m2/mine/bind_alipay/';   // 绑定支付宝

$wallet_info = array(
    'user_id' => $user_id,
    'nickname' => $user_info['nickname'],// get_user_nickname_by_user_id($user_id),
    'icon' => get_user_icon($user_id, 165, TRUE),
    'location_id' => $user_info['location_id'],
//    'city_name' => get_poco_location_name_by_location_id($user_info ['location_id']), // 所在城市
    // 优惠券
    'coupon_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($coupon_url) . '&wifi_url=' .
        urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $coupon_url)) . '&showtitle=1',
    // 充值
    'recharge_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($recharge_url) .
        '&wifi_url=' . urlencode($recharge_url) . '&showtitle=1',
    // 提现
    'withdraw_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($withdraw_url) . '&wifi_url=' .
        urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $withdraw_url)),
    // 账单
    'bill_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($bill_url) .
        '&wifi_url=' . urlencode($bill_url) . '&showtitle=1', // 账单列表
    // 绑定支付宝
    'alipay_url' => 'yueyue://goto?type=inner_web&url=' . urlencode($alipay_url) . '&wifi_url=' .
        urlencode(str_replace('yp.yueus.com', 'yp-wifi.yueus.com', $alipay_url)) . '&showtitle=2',
);

$options['data'] = $wallet_info;
return $cp->output($options);
