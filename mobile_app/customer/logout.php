<?php

/**
 * 退出登录
 * 
 * @author heyaohua
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);  // 暂时 不验证TOKEN ( 2015-9-11 )
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];

$role = 'yuebuyer';
$obj = POCO::singleton('pai_nfc_class');
//$obj->mobile_logout($user_id, $role);

$data['tips'] = '退出成功!';
$options['data'] = $data;

$cp->output($options);
