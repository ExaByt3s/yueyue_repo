<?php
/**
 * @desc:   ������˲�����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/28
 * @Time:   12:13
 * version: 1.0
 */

include_once('common.inc.php');
$text_examine_v2_obj = new pai_text_examine_v2_class();

$act = trim($_INPUT['act']);
$retUrl = $_SERVER['HTTP_REFERER'];

if(strlen($act) <1) js_pop_msg_v2('�Ƿ�����');
$ids = $_INPUT ['ids']; //������������һάID
if (empty($ids)) js_pop_msg_v2('������������Ϊ��');

if($act == 'pass')//ͨ������
{
    $ret = $text_examine_v2_obj->text_examine_pass($ids);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('������˳ɹ�',false,$retUrl);
    js_pop_msg_v2('�������ʧ��',false,$retUrl);
}
elseif($act == 'del') //ɾ������
{
    $ret = $text_examine_v2_obj->text_examine_del($ids);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('����ɾ���ɹ�',false,$retUrl);
    js_pop_msg_v2('����ɾ��ʧ��',false,$retUrl);
}
elseif($act == 'pass_to_del')//���Ѿ�������ֵ�ɾ��
{
    $ymonth = trim($_INPUT['ymonth']);//�������ֵ����¹���
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $text_examine_v2_obj->text_pass_to_del($ids,$date);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('����ɾ���ɹ�',false,$retUrl);
    js_pop_msg_v2('����ɾ��ʧ��',false,$retUrl);
}
elseif($act == 'del_to_pass')//��ɾ�������ֵ���˵�����
{
    $ymonth = trim($_INPUT['ymonth']);//�������ֵ����¹���
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $text_examine_v2_obj->text_del_to_pass($ids,$date);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('�ָ����ֳɹ�',false,$retUrl);
    js_pop_msg_v2('�ָ�����ʧ��',false,$retUrl);
}

js_pop_msg_v2('�Ƿ�����',false,$retUrl);