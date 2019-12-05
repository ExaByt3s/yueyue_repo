<?php
/**
 * @desc:   信息操作类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/22
 * @Time:   17:19
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
include_once('common.inc.php');
include_once('pai_mall_message_class.inc.php');
$mall_message_obj = new pai_mall_message_class();

//参数接收
$act = trim($_INPUT['act']);
$type_id = intval($_INPUT['type_id']);
$location_id = intval($_INPUT['location_id']);
$role = trim($_INPUT['role']);
$only_send_online = intval($_INPUT['only_send_online']);
$start_time = trim($_INPUT['start_time']);
$end_time = trim($_INPUT['end_time']);
$start_interval = trim($_INPUT['start_interval']);
$end_interval = trim($_INPUT['end_interval']);
$send_uid = intval($_INPUT['send_uid']) ? intval($_INPUT['send_uid']) : 10002;
$user_str = trim($_INPUT['user_str']);
$user_str = trim(str_replace('，', ',', $user_str),",");
$card_style = intval($_INPUT['card_style']) ? intval($_INPUT['card_style']) :  2; //卡片类型
$card_text1 = trim($_INPUT['card_text1']); //卡片描述
if(strlen($card_text1)>0) $card_text1 = str_replace('<br rel=auto>', "\r\n", $card_text1);
$card_text2 = trim($_INPUT['card_text2']); //金额
$card_title = trim($_INPUT['card_title']);
$link_url  = trim($_INPUT['link_url']);
$wifi_url = trim($_INPUT['wifi_url']);
$content = trim($_INPUT['content']);//内容
if(strlen($content)>0) $content = str_replace('<br rel=auto>', "\r\n", $content);

$url_type = trim($_INPUT['url_type']);//内圈链接和外圈链接


