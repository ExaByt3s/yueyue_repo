<?php
/**
 * 接收微信的信息
 * @author Henry
 * @copyright 2014-12-31
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');

$bind_id = intval($_GET['bid']);
$signature = trim($_GET['signature']);
$timestamp = trim($_GET['timestamp']);
$nonce = trim($_GET['nonce']);
$echostr = trim($_GET['echostr']);
$push_xml = trim($GLOBALS["HTTP_RAW_POST_DATA"]);

//临时日志
pai_log_class::add_log(array(), 'receive' , 'pai_weixin_notify');

//获取绑定信息
$bind_info = $weixin_helper_obj->get_bind_info($bind_id);
if( empty($bind_info) )
{
	exit();
}

//获取token
$token = trim($bind_info['token']);
if( strlen($token)<1 )
{
	exit();
}

//检查签名
$authorized = $weixin_helper_obj->wx_check_signature($token, $timestamp, $nonce, $signature);
if( !$authorized )
{
	exit();
}

//服务器配置
if( strlen($echostr)>0 )
{
	echo $echostr;
	exit();
}

//检查消息
if( strlen($push_xml)<1 )
{
	exit();
}

$push_data = $weixin_helper_obj->push_xml_to_array($push_xml);
if( empty($push_data) )
{
	exit();
}

//检查与open_id是否一致
$ToUserName = trim($push_data['ToUserName']);
$open_id = trim($bind_info['open_id']);
if( strlen($open_id)>0 && $open_id!=$ToUserName )
{
	exit();
}

//获取回复数据
$reply_data = $weixin_helper_obj->wx_get_reply_by_push($bind_id, $push_data);

//临时日志
pai_log_class::add_log($reply_data, 'reply' , 'pai_weixin_notify');

if( empty($reply_data) )
{
	echo 'success';
	exit();
}

//输出回复消息
$reply_xml = $weixin_helper_obj->reply_array_to_xml($reply_data);
echo $reply_xml;
