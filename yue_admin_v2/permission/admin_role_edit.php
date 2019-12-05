<?php
/**
 * @desc:   角色编辑操作类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   10:04
 * version: 1.0
 */

include_once('common.inc.php');
check_auth($yue_login_id,'admin_role_edit');//权限控制

$admin_log_obj  = POCO::singleton('pai_admin_log_class');//操作log
$admin_role_index_obj  = POCO::singleton('pai_admin_role_index_class');//角色类
$admin_op_obj  = POCO::singleton('pai_admin_op_class');//操作类
$tpl = new SmartTemplate( 'admin_role_edit.tpl.htm' );
$role_id = intval($_INPUT['role_id']);
$role_name = trim($_INPUT['role_name']);
$sort = intval($_INPUT['sort']);
$op_arr = $_INPUT['op_id'];

$module = 'role';
$admin_log_obj->add_admin_log($module,$act);//添加log

$setParam = array('act' => 'insert');
if($act == 'insert')//插入
{
    if(strlen($role_name) <1) js_pop_msg_v2('角色名称不能为空');
    $retID = $admin_role_index_obj->add_info_role($role_name,$sort,$op_arr);
    if($retID >0) js_pop_msg_v2('添加角色成功',false,'admin_role_list.php');
    js_pop_msg_v2('添加角色失败');
}
elseif($act == 'edit')//编辑
{
    if($role_id <1) js_pop_msg_v2('非法操作');
    $setParam['act'] = 'update';
    $ret = $admin_role_index_obj->get_info_by_role_id($role_id);
    $tpl->assign($ret);
}
elseif($act == 'update')//更新
{
    if(strlen($role_name) <1) js_pop_msg_v2('角色名称不能为空');
    $retID = $admin_role_index_obj->update_info_role($role_id,$role_name,$sort,$op_arr);
    if($retID >0) js_pop_msg_v2('编辑角色成功',false,'admin_role_list.php');
    js_pop_msg_v2('编辑角色失败');
}
elseif($act == 'del')
{
    $role_id = intval($role_id);
    if($role_id <1) js_pop_msg_v2('非法操作',false,'admin_role_list.php',false);
    $retID =  $admin_role_index_obj->del_info_by_role_id($role_id);
    if($retID >0)
    {
        js_pop_msg_v2('删除成功',false,'admin_role_list.php',false);
    }
    js_pop_msg_v2('删除失败',false,'admin_role_list.php',false);
}

$option = $admin_op_obj->get_op_option_by_role_id($role_id);

$tpl->assign('option',$option);
$tpl->assign($setParam);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();