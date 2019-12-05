<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require('protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];
$app_name = $client_data['data']['param']['app_name'];

$user_id = 105081;
$app_name = 'poco_yuepai_android';

// 获取用户的授权信息
$access_info = $cp->get_access_info($user_id, $app_name);
print_r($access_info);

// 验证授权是否过期
if ($cp->is_access_expire(NULL, NULL, NULL, $access_info)) {
    // 刷新授权
    $access_info = $cp->refresh_access_info(NULL, NULL, NULL, $access_info);
}
$options['data'] = $access_info;

return $cp->output($options);
