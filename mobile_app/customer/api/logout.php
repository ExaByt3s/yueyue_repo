<?php

/**
 * 退出登录
 * 
 * @author heyaohua
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../../protocol_common.inc.php");

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input();

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];

$role = 'yuebuyer';
$obj = POCO::singleton('pai_nfc_class');
$obj->mobile_logout($user_id, $role);

$data['tips'] = '退出成功!';
$options['data'] = $data;

$cp->output($options);
