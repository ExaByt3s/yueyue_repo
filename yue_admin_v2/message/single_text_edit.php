<?php
/**
 * @desc:   ���͵�����������Ϣ
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/29
 * @Time:   16:42
 * version: 1.0
 */
include_once('common.inc.php');
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_send_single_message_class.inc.php');
$single_message_obj = new pai_send_single_message_class();

$tpl = new SmartTemplate( TEMPLATES_ROOT.'single_text_edit.tpl.htm');


$act = trim($_INPUT['act']);
$user_id = (int)$_INPUT['user_id'];
$role = trim($_INPUT['role']);
$type = trim($_INPUT['type']);//���͸�ʽ
$send_uid = intval($_INPUT['send_uid']);
$content = trim($_INPUT['content']);
$content = str_replace(array('<br rel=auto>','<br/>','<br>'), "\r\n", $content);
$link_url = trim($_INPUT['link_url']);
$wifi_url = trim($_INPUT['wifi_url']);
$is_change_to_yueyue = (int)$_INPUT['is_change_to_yueyue'];

//��ʼ������Ϣ
if($act == 'send')
{
    if($user_id <1) js_pop_msg_v2('�û�ID����Ϊ��');
    if(strlen($role) <1) js_pop_msg_v2('��ɫ����Ϊ��');
    if(strlen($type) <1) js_pop_msg_v2('���Ͳ���Ϊ��');
    if($send_uid <1) js_pop_msg_v2('�����û�����Ϊ��');
    if(strlen($content) <1) js_pop_msg_v2('���ݲ���Ϊ��');
    $ret = $single_message_obj->add_single_text_info($type,$send_uid,$user_id,$role,$content,$link_url,$wifi_url,$is_change_to_yueyue);
    if(!is_array($ret)) $ret = array();
    $status = (int)$ret['code'];
    if($status >0) js_pop_msg_v2('���ͳɹ�',false,'?act=007');
    print_r($ret);
    js_pop_msg_v2("����ʧ��");
    exit;
}

$tpl->output();


