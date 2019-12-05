<?php
/**
 * @desc:   ��ɫ�༭������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   10:04
 * version: 1.0
 */

include_once('common.inc.php');
check_auth($yue_login_id,'admin_role_edit');//Ȩ�޿���

$admin_log_obj  = POCO::singleton('pai_admin_log_class');//����log
$admin_role_index_obj  = POCO::singleton('pai_admin_role_index_class');//��ɫ��
$admin_op_obj  = POCO::singleton('pai_admin_op_class');//������
$tpl = new SmartTemplate( 'admin_role_edit.tpl.htm' );
$role_id = intval($_INPUT['role_id']);
$role_name = trim($_INPUT['role_name']);
$sort = intval($_INPUT['sort']);
$op_arr = $_INPUT['op_id'];

$module = 'role';
$admin_log_obj->add_admin_log($module,$act);//���log

$setParam = array('act' => 'insert');
if($act == 'insert')//����
{
    if(strlen($role_name) <1) js_pop_msg_v2('��ɫ���Ʋ���Ϊ��');
    $retID = $admin_role_index_obj->add_info_role($role_name,$sort,$op_arr);
    if($retID >0) js_pop_msg_v2('��ӽ�ɫ�ɹ�',false,'admin_role_list.php');
    js_pop_msg_v2('��ӽ�ɫʧ��');
}
elseif($act == 'edit')//�༭
{
    if($role_id <1) js_pop_msg_v2('�Ƿ�����');
    $setParam['act'] = 'update';
    $ret = $admin_role_index_obj->get_info_by_role_id($role_id);
    $tpl->assign($ret);
}
elseif($act == 'update')//����
{
    if(strlen($role_name) <1) js_pop_msg_v2('��ɫ���Ʋ���Ϊ��');
    $retID = $admin_role_index_obj->update_info_role($role_id,$role_name,$sort,$op_arr);
    if($retID >0) js_pop_msg_v2('�༭��ɫ�ɹ�',false,'admin_role_list.php');
    js_pop_msg_v2('�༭��ɫʧ��');
}
elseif($act == 'del')
{
    $role_id = intval($role_id);
    if($role_id <1) js_pop_msg_v2('�Ƿ�����',false,'admin_role_list.php',false);
    $retID =  $admin_role_index_obj->del_info_by_role_id($role_id);
    if($retID >0)
    {
        js_pop_msg_v2('ɾ���ɹ�',false,'admin_role_list.php',false);
    }
    js_pop_msg_v2('ɾ��ʧ��',false,'admin_role_list.php',false);
}

$option = $admin_op_obj->get_op_option_by_role_id($role_id);

$tpl->assign('option',$option);
$tpl->assign($setParam);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();