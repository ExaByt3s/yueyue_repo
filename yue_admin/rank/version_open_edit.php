<?php
/**
 * @desc:   �汾��ͨ�ļ� [�޸�ҳ�����ҳ]
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/8
 * @Time:   11:16
 * version: 1.0
 */

include('common.inc.php');
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$version_open_obj = POCO::singleton('pai_version_open_class');

$tpl = new SmartTemplate("version_open_edit.tpl.htm");



$act = trim($_INPUT['act']);



//����
if($act == 'insert')
{
    $version_num = trim($_INPUT['version_num']);
    if(strlen($version_num) <1)   js_pop_msg("�汾�Ų���Ϊ��!");
    $info = $version_open_obj->add_info($version_num);
    if($info >0) js_pop_msg('��ӳɹ�');
    js_pop_msg('���ʧ��,���ܸİ汾���Ѿ�������');
}
elseif($act == 'edit')
{
    $id = intval($_INPUT['id']);
    if($id <1)  js_pop_msg('�Ƿ�����');
    $ret =  $version_open_obj->get_info($id);
    $tpl->assign($ret);
    $tpl->assign('act','update');
    $tpl->output();
    exit;
}
//��������
elseif($act == 'update')
{
    $id = intval($_INPUT['id']);
    $version_num = trim($version_num);

    if($id <1)  js_pop_msg('�Ƿ�����');
    if(strlen($version_num) <1) js_pop_msg("�Ƿ�����");
    $info = $version_open_obj->update_info($id,$version_num);
    if($info)js_pop_msg('�������ݳɹ�');
    js_pop_msg('��������ʧ��');
}
//ɾ������
elseif($act == 'del')
{
    $id = intval($_INPUT['id']);
    if($id <1)js_pop_msg("�Ƿ�����");
    $info = $version_open_obj->del_info($id);
    if($info) js_pop_msg('ɾ�����ݳɹ�','','version_open.php?act=list');
     js_pop_msg('ɾ������ʧ��','','version_open.php?act=list');

}

$tpl->assign('act','insert');
$tpl->output();










