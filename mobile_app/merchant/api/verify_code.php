<?php

/**
 * 数字码 接口
 * 
 * @since 2015-7-2
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../../protocol_common.inc.php");

//上线需要删除
define('G_PAI_ECPAY_DEV', 1);

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input();

$user_id = $client_data['data']['param']['user_id'];
$code = urldecode($client_data['data']['param']['code']);

$qrcode_obj = POCO::singleton('pai_activity_code_class');
$ret = $qrcode_obj->verify_mall_code($user_id,$code);

if($ret['result']==1)
{
	$options['data']['code'] = '1';
	$options['data']['msg'] = '验证成功';
	$options['data']['url'] = 'yueseller://goto?type=inner_app&pid=1250022&order_sn=' . $ret['order_sn'];
}
else
{
	$options['data']['code'] = '0';
	$options['data']['msg'] = $ret['message'];
}

$cp->output($options);


