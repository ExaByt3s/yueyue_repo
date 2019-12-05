<?php
/*
 * 注册
 */
require_once('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// 获取客户端的数据
$cp = new yue_protocol_system();
// 获取用户的授权信息
$client_data = $cp->get_input ( array ('be_check_token' => false ) );

$user_id        = $client_data['data']['param']['user_id'];
$access_token   = $client_data['data']['param']['access_token'];
$refresh_token  = $client_data['data']['param']['refresh_token'];
$app_name       = $client_data['data']['param']['app_name'];


$access_info = $cp->get_access_info($user_id, $app_name);
if($access_info[expire_time]-time() <= 3600*6)
{
    if($refresh_token)
    {
        $access_info = $cp->refresh_access_info_by_refresh_token($user_id, $app_name, $refresh_token);
    }else{
        $access_info = $cp->refresh_access_info($user_id, $app_name);
    }
}


$options['data'] = $access_info;

$cp->output($options);

?>