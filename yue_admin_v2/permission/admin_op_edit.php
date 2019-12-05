<?php
/**
 * @desc:   操作控制--编辑
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   12:19
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'admin_op_edit');//权限控制

$admin_log_obj  = POCO::singleton('pai_admin_log_class');//操作log
$admin_op_obj  = POCO::singleton('pai_admin_op_class');//操作类
$tpl = new SmartTemplate( 'admin_op_edit.tpl.htm' );

$act = trim($_INPUT['act']);
$op_id      = intval($_INPUT['op_id']);
$op_type_id = intval($_INPUT['op_type_id']);
$op_is_nav = intval($_INPUT['op_is_nav']);
$op_name = trim($_INPUT['op_name']);
$op_code = trim($_INPUT['op_code']);
$op_url = trim($_INPUT['op_url']);
$op_location = trim(str_replace('，', ',', $_INPUT['op_location']),",");
$parent_id = trim($_INPUT['parent_id']);
$sort = intval($_INPUT['sort']);

$setParam = array('act' =>'insert','selId'=>0);

$module = 'op';
$admin_log_obj->add_admin_log($module,$act);//添加log

if($act == 'insert')//插入
{
    if(strlen($op_name) <1) js_pop_msg_v2('操作名不能为空');
    if(strlen($op_code) <1) js_pop_msg_v2('操作代码不能为空');
    $option = array();
    if(strlen($op_location) >0) $option['op_location'] = $op_location;
    $retID = $admin_op_obj->add_info_op($op_type_id,$op_name,$op_code,$op_url,$parent_id,$sort,$op_is_nav,$option);
    if($retID >0) js_pop_msg_v2('添加成功',false,'admin_op_list.php');
    js_pop_msg_v2('添加失败');
}
elseif($act == 'update')//更新
{
    if(strlen($op_name) <1) js_pop_msg_v2('操作名不能为空');
    if(strlen($op_code) <1) js_pop_msg_v2('操作代码不能为空');

    if($op_id == $parent_id) js_pop_msg_v2('所属操作,不能选择自身');
    if($parent_id >0)
    {
        $bloo = false;
        $cat_list = $admin_op_obj->is_check_parent_id($op_id);
        if(in_array($parent_id,$cat_list))
        {
            js_pop_msg_v2('所选择的上级操作不能是当前操作或者当前操作的下级操作!');
        }
    }
    $option = array();//拓展参数
    if(strlen($op_location) >0) $option['op_location'] = $op_location;
    $retID = $admin_op_obj->update_info_op($op_id,$op_type_id,$op_name,$op_code,$op_url,$parent_id,$sort,$op_is_nav,$option);
    if($retID >0) js_pop_msg_v2('编辑成功',false,'admin_op_list.php');
    js_pop_msg_v2('编辑失败');
}
elseif($act == 'edit')//编辑
{
    if($op_id <1) js_pop_msg_v2('非法操作');
    $setParam['act'] = 'update';
    $ret = $admin_op_obj->get_op_info_by_op_id($op_id);
    $setParam['selId'] = intval($ret['parent_id']);
    $tpl->assign($ret);
}
elseif($act == 'sort')//重新排序
{
    $op = $_INPUT['op'];
    $retID = $admin_op_obj->op_id_sort_again($op);
    if($retID >0) js_pop_msg_v2('排序成功',false,'admin_op_list.php');
    js_pop_msg_v2('排序失败');
}
elseif($act == 'del')
{
    $op_id = intval($op_id);
    if($op_id <1) js_pop_msg_v2('非法操作',false,'admin_op_list.php',false);
    //作为父类查看有没有子类
    $ret = $admin_op_obj->get_op_info_by_parent_id($op_id);
    if(is_array($ret) && !empty($ret))
    {
        js_pop_msg_v2('存在子类,无法删除',false,'admin_op_list.php',false);
    }
    $retID = $admin_op_obj->del_op_info_by_op_id($op_id);
    if($retID >0)
    {
        js_pop_msg_v2('删除成功',false,'admin_op_list.php',false);
    }
    js_pop_msg_v2('删除失败',false,'admin_op_list.php',false);
}

$option = $admin_op_obj->get_op_sort_option($setParam['selId']);//选择部分
$tpl->assign('option',$option);
$tpl->assign($setParam);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();