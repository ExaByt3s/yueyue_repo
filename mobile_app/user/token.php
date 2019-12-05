<?php

/**
 * 刷新 TOKEN
 * 
 * @editor willike <chenwb@yueus.com>
 * @since 2015-9-11
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];
$refresh_token = $client_data['data']['param']['refresh_token'];
$app_name = $client_data['data']['app_name'];

// 获取用户的授权信息
$access_info = $cp->get_access_info($user_id, $app_name, TRUE, FALSE);

// 验证授权是否过期
if ($cp->is_access_expire($user_id, $app_name, NULL, $access_info)) {
    // 刷新授权
    $access_info = $cp->refresh_access_info(NULL, NULL, NULL, $access_info);
}
$options['data'] = $access_info;
return $cp->output($options);
