<?php
/**
 * @desc:   ��Ʒ��˿�����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/8
 * @Time:   14:02
 * version: 2.0
 */
include_once ('common.inc.php');
$pic_examine_v2_obj = new pai_pic_examine_v2_class();

$act = trim($_INPUT['act']);
$retUrl = $_SERVER['HTTP_REFERER'];

if(strlen($act) <1) js_pop_msg_v2('�Ƿ�����');
$ids = $_INPUT ['ids']; //��������ͼƬһάID

if (empty($ids)) js_pop_msg_v2('������������Ϊ��');

//��������
if($act == 'pass')//ͼƬ���ͨ��
{
    $ret = $pic_examine_v2_obj->pic_examine_pass($ids);
    $retID = intval($ret['code']);
    if($retID >0) js_pop_msg_v2('ͼƬ��˳ɹ�',false,$retUrl);
    js_pop_msg_v2('ͼƬ���ʧ��',false,$retUrl);
}
elseif($act == 'del')//ͼƬ���ɾ��
{
    $ids = trim($ids);//���ﴫ�������Ǹ��ַ���122,333
    $img_type = trim($_INPUT['img_type']);
    $tpl_id = intval($_INPUT['tpl_id']);//ģ��ID
    $tpl_detail = trim($_INPUT['tpl_detail']);//ģ�����ݣ�������ģ��ID����
    if(!in_array($img_type,$type_img_arr)) js_pop_msg_v2('�Ƿ�����');
    $ret = $pic_examine_v2_obj->pic_examine_del($img_type,$ids,$tpl_id,$tpl_detail);
    $retID = $ret['code'];
    if($retID >0) js_pop_msg_v2('ɾ��ͼƬ�ɹ�',true,"pic_examine_list.php?act={$img_type}");
    js_pop_msg_v2('ɾ��ͼƬʧ��',true,"pic_examine_list.php?act={$img_type}");
}
elseif($act == 'pass_to_del') //��ͨ��ȥ��ɾ��
{
    $ymonth = trim($_INPUT['ymonth']);//����ͼƬ�����¹���
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $pic_examine_v2_obj->pic_pass_to_del($ids,$date);
    $retID = intval($ret['code']);
    //print_r($ret);exit;
    if($retID >0) js_pop_msg_v2('ɾ��ͼƬ�ɹ�',false,$retUrl);
    js_pop_msg_v2('ɾ��ͼƬʧ��',false,$retUrl);
}
elseif($act == 'del_to_pass')//��ɾ����ͨ��
{
    $ymonth = trim($_INPUT['ymonth']);//����ͼƬ�����¹���
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $pic_examine_v2_obj->pic_del_to_pass($ids,$date);
    $retID = intval($ret['code']);
    if($retID >0) js_pop_msg_v2('�ָ�ͼƬ�ɹ�',false,$retUrl);
    js_pop_msg_v2('�ָ�ͼƬʧ��',false,$retUrl);
}
else
{
    js_pop_msg_v2('�Ƿ�����');
}