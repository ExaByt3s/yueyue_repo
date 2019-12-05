<?php
/**
 * @desc:   单条发送卡片信息
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/30
 * @Time:   10:35
 * version: 1.0
 */
include_once('common.inc.php');
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_send_single_message_class.inc.php');
$single_message_obj = new pai_send_single_message_class();
$tpl = new SmartTemplate( TEMPLATES_ROOT.'single_card_edit.tpl.htm');


$act = trim($_INPUT['act']);
$role = trim($_INPUT['role']);
$send_uid = (int)$_INPUT['send_uid'];
$user_id = (int)$_INPUT['user_id'];
$card_style = (int)$_INPUT['card_style']; //卡片类型
$card_text1 = trim($_INPUT['card_text1']); //卡片描述
$card_text1 = str_replace(array('<br rel=auto>','<br/>','<br>','<br rel=auto/>','<br rel="auto">','<br rel="auto"/>'), "\r\n", $card_text1);
$card_text2 = trim($_INPUT['card_text2']); //金额
$card_title = trim($_INPUT['card_title']);
$link_url  = trim($_INPUT['link_url']);
$wifi_url = trim($_INPUT['wifi_url']);
$is_change_to_yueyue = (int)$_INPUT['is_change_to_yueyue'];
$type = 'card';
//开始发送卡片信息
if($act == 'send')
{
    if(strlen($type) <1) js_pop_msg_v2('格式不能为空');
    if(strlen($role) <1) js_pop_msg_v2('角色不能为空');
    if($send_uid <1) js_pop_msg_v2('发送者不能为空');
    if($user_id <1) js_pop_msg_v2('用户ID不能为空');
    if($card_style <1) js_pop_msg_v2('卡片类型不能为空');
    if(strlen($card_text1)<1) js_pop_msg_v2('卡片内容不能为空');
    if(strlen($card_title) <1) js_pop_msg_v2('卡片标题不能为空');
    if(strlen($link_url) <1) js_pop_msg_v2('移动跳转链接不能为空');
    if(strlen($wifi_url) <1) js_pop_msg_v2('wif跳转链接不能为空');
    $ret = $single_message_obj->add_single_text_info($type,$send_uid,$user_id,$role,'',$link_url,$wifi_url,$is_change_to_yueyue,$card_style,$card_text1,$card_title,$card_text2);
    if(!is_array($ret)) $ret = array();
    $status = (int)$ret['code'];
    if($status >0) js_pop_msg_v2('发送成功',false,'?act=007');
    print_r($ret);
    js_pop_msg_v2("发送失败");
    exit;

}

$tpl->output();