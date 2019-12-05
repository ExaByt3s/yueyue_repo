<?php
/**
 * @desc:   ��������--�༭
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   12:19
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'admin_op_edit');//Ȩ�޿���

$admin_log_obj  = POCO::singleton('pai_admin_log_class');//����log
$admin_op_obj  = POCO::singleton('pai_admin_op_class');//������
$tpl = new SmartTemplate( 'admin_op_edit.tpl.htm' );

$act = trim($_INPUT['act']);
$op_id      = intval($_INPUT['op_id']);
$op_type_id = intval($_INPUT['op_type_id']);
$op_is_nav = intval($_INPUT['op_is_nav']);
$op_name = trim($_INPUT['op_name']);
$op_code = trim($_INPUT['op_code']);
$op_url = trim($_INPUT['op_url']);
$op_location = trim(str_replace('��', ',', $_INPUT['op_location']),",");
$parent_id = trim($_INPUT['parent_id']);
$sort = intval($_INPUT['sort']);

$setParam = array('act' =>'insert','selId'=>0);

$module = 'op';
$admin_log_obj->add_admin_log($module,$act);//���log

if($act == 'insert')//����
{
    if(strlen($op_name) <1) js_pop_msg_v2('����������Ϊ��');
    if(strlen($op_code) <1) js_pop_msg_v2('�������벻��Ϊ��');
    $option = array();
    if(strlen($op_location) >0) $option['op_location'] = $op_location;
    $retID = $admin_op_obj->add_info_op($op_type_id,$op_name,$op_code,$op_url,$parent_id,$sort,$op_is_nav,$option);
    if($retID >0) js_pop_msg_v2('��ӳɹ�',false,'admin_op_list.php');
    js_pop_msg_v2('���ʧ��');
}
elseif($act == 'update')//����
{
    if(strlen($op_name) <1) js_pop_msg_v2('����������Ϊ��');
    if(strlen($op_code) <1) js_pop_msg_v2('�������벻��Ϊ��');

    if($op_id == $parent_id) js_pop_msg_v2('��������,����ѡ������');
    if($parent_id >0)
    {
        $bloo = false;
        $cat_list = $admin_op_obj->is_check_parent_id($op_id);
        if(in_array($parent_id,$cat_list))
        {
            js_pop_msg_v2('��ѡ����ϼ����������ǵ�ǰ�������ߵ�ǰ�������¼�����!');
        }
    }
    $option = array();//��չ����
    if(strlen($op_location) >0) $option['op_location'] = $op_location;
    $retID = $admin_op_obj->update_info_op($op_id,$op_type_id,$op_name,$op_code,$op_url,$parent_id,$sort,$op_is_nav,$option);
    if($retID >0) js_pop_msg_v2('�༭�ɹ�',false,'admin_op_list.php');
    js_pop_msg_v2('�༭ʧ��');
}
elseif($act == 'edit')//�༭
{
    if($op_id <1) js_pop_msg_v2('�Ƿ�����');
    $setParam['act'] = 'update';
    $ret = $admin_op_obj->get_op_info_by_op_id($op_id);
    $setParam['selId'] = intval($ret['parent_id']);
    $tpl->assign($ret);
}
elseif($act == 'sort')//��������
{
    $op = $_INPUT['op'];
    $retID = $admin_op_obj->op_id_sort_again($op);
    if($retID >0) js_pop_msg_v2('����ɹ�',false,'admin_op_list.php');
    js_pop_msg_v2('����ʧ��');
}
elseif($act == 'del')
{
    $op_id = intval($op_id);
    if($op_id <1) js_pop_msg_v2('�Ƿ�����',false,'admin_op_list.php',false);
    //��Ϊ����鿴��û������
    $ret = $admin_op_obj->get_op_info_by_parent_id($op_id);
    if(is_array($ret) && !empty($ret))
    {
        js_pop_msg_v2('��������,�޷�ɾ��',false,'admin_op_list.php',false);
    }
    $retID = $admin_op_obj->del_op_info_by_op_id($op_id);
    if($retID >0)
    {
        js_pop_msg_v2('ɾ���ɹ�',false,'admin_op_list.php',false);
    }
    js_pop_msg_v2('ɾ��ʧ��',false,'admin_op_list.php',false);
}

$option = $admin_op_obj->get_op_sort_option($setParam['selId']);//ѡ�񲿷�
$tpl->assign('option',$option);
$tpl->assign($setParam);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();