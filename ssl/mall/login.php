<?php

/**
 * 登录验证
 * 
 * @since long long ago
 */
require('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// 获取客户端的数据
$cp = new yue_protocol_system();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => FALSE));

$phone = $client_data ['data']['param']['phone'];
$pwd = $client_data ['data']['param']['pwd'];
$app_name = $client_data ['data']['param']['app_name'];
$app_name = empty($app_name) ? $client_data ['data'] ['app_name'] : $app_name;
$app_name = $app_name ? $app_name : 'poco_yuepai_android';

$pai_user_obj = POCO::singleton('pai_user_class');
$mall_obj = POCO::singleton('pai_mall_seller_class');

$user_id = $pai_user_obj->user_login($phone, $pwd);

$log_arr['user_id'] = $user_id;
pai_log_class::add_log($log_arr, 'ssl_login', 'ssl_login');

if ($user_id) {
    $seller_info = $mall_obj->get_seller_info($user_id, 2);
    $seller_status = $seller_info['seller_data']['status'];
    if (!$seller_info or $seller_status == 2) {
        //非商家
        yue_app_out_put('not_seller', 0);
    }

    yue_app_out_put('login_success', 1);
} else {
    yue_app_out_put('login_error', 0);
}
?>