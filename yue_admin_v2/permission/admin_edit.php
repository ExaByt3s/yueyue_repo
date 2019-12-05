<?php
/**
 * @desc:   管理员添加操作
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/27
 * @Time:   15:43
 * version: 1.0
 */

include_once('common.inc.php');
$admin_log_obj  = POCO::singleton('pai_admin_log_class');//操作log
$admin_index_obj  = POCO::singleton('pai_admin_index_class');//管理员类
$admin_role_index_obj  = POCO::singleton('pai_admin_role_index_class');//角色类
$admin_op_obj  = POCO::singleton('pai_admin_op_class');//操作类
$tpl = new SmartTemplate("admin_edit.tpl.htm");

$act = trim($_INPUT['act']);
$user_id = intval($_INPUT['user_id']);
$status  = intval($_INPUT['status']);//状态
$real_name = trim($_INPUT['real_name']);//真实姓名
$department = trim($_INPUT['department']);//部门
$role_id = $_INPUT['role_id'];
$op_arr = $_INPUT['op_id'];

$module = 'admin';
$admin_log_obj->add_admin_log($module,$act);//添加log

$setParam = array('act'=> 'insert'); //初始化
if($act == 'insert')//插入
{
    $retID = $admin_index_obj->add_info_index($user_id,$status,$real_name,$department,$role_id,$op_arr);
    if($retID >0)
    {
        js_pop_msg_v2('添加管理员成功',false,'admin_list.php');
    }
    js_pop_msg_v2('添管理员失败');
}
elseif($act == 'edit')//修改
{
    check_auth($yue_login_id,'admin_edit');
    $setParam['act'] = 'update';
    $ret = $admin_index_obj->get_info_by_user_id($user_id);
    if(!is_array($ret)) $ret = array();
    $ret['nickname'] = get_user_nickname_by_user_id($ret['user_id']);
    $tpl->assign($ret);
}
elseif($act == 'update')//更新
{
    check_auth($yue_login_id,'admin_edit');
    $retID = $admin_index_obj->update_info_index($user_id,$status,$real_name,$department,$role_id,$op_arr);
    if($retID >0)
    {
        js_pop_msg_v2('修改管理员成功',false,'admin_list.php');
    }
    js_pop_msg_v2('修改管理员失败');
}
elseif($act == 'del')
{
    check_auth($yue_login_id,'admin_del');
    $retID = $admin_index_obj->del_admin_index_by_user_id($user_id);
    if($retID >0)
    {
        js_pop_msg_v2('删除管理员成功',false,'admin_list.php',false);
    }
    js_pop_msg_v2('删除管理员失败',false,'admin_list.php',false);
}
elseif($act == 'params')
{
    $ret = array('state'=>0,'msg'=>'');
    if($user_id <1)
    {
        $ret['state'] = -1;
        echo json_encode($ret);
        exit;
    }
    $nickname = get_user_nickname_by_user_id($user_id);
    if(strlen($nickname) <1)
    {
        $ret['state'] = -2;
        echo json_encode($ret);
        exit;
    }
    $ret['state'] = 1;
    $ret['msg'] = iconv('GBK','utf-8',$nickname);
    echo json_encode($ret);
    exit;
}


check_auth($yue_login_id,'admin_add');
$role_ret = $admin_role_index_obj->get_role_sort_by_user_id($user_id);

$option =  $admin_op_obj->get_admin_op_by_user_id($user_id);

$tpl->assign('option',$option);
$tpl->assign('role_ret',$role_ret);
$tpl->assign($setParam);
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
