<?php

/*
 * 登录验证
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
require_once('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
// 获取客户端的数据
$cp = new yue_protocol_system();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$phone = $client_data ['data'] ['param'] ['phone'];
$pwd = $client_data ['data'] ['param'] ['pwd'];
$app_name = $client_data ['data'] ['param'] ['app_name'];
$app_name = empty($app_name) ? $client_data ['data'] ['app_name'] : $app_name;

$app_name = $app_name ? $app_name : 'poco_yuepai_android';

$pai_user_obj = POCO::singleton('pai_user_class');
$mall_obj = POCO::singleton('pai_mall_seller_class');

$user_id = $pai_user_obj->user_login($phone, $pwd);


$log_arr['user_id'] = $user_id;
pai_log_class::add_log($log_arr, 'ssl_login', 'ssl_login');

if ($user_id) {
    yue_app_out_put('login_success', 1);
} else {
    yue_app_out_put('login_error', 0);
}
?>