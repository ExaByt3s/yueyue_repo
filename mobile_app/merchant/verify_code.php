<?php

/**
 * 数字码 接口
 * 
 * @since 2015-7-2
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$code = $client_data['data']['param']['code'];

$code = str_replace(" ","",$code);

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


