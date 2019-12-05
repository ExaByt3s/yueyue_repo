<?php
/**
 * @desc:   版本开通文件 [修改页和添加页]
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



//插入
if($act == 'insert')
{
    $version_num = trim($_INPUT['version_num']);
    if(strlen($version_num) <1)   js_pop_msg("版本号不能为空!");
    $info = $version_open_obj->add_info($version_num);
    if($info >0) js_pop_msg('添加成功');
    js_pop_msg('添加失败,可能改版本号已经存在了');
}
elseif($act == 'edit')
{
    $id = intval($_INPUT['id']);
    if($id <1)  js_pop_msg('非法操作');
    $ret =  $version_open_obj->get_info($id);
    $tpl->assign($ret);
    $tpl->assign('act','update');
    $tpl->output();
    exit;
}
//更新数据
elseif($act == 'update')
{
    $id = intval($_INPUT['id']);
    $version_num = trim($version_num);

    if($id <1)  js_pop_msg('非法操作');
    if(strlen($version_num) <1) js_pop_msg("非法操作");
    $info = $version_open_obj->update_info($id,$version_num);
    if($info)js_pop_msg('更新数据成功');
    js_pop_msg('更新数据失败');
}
//删除数据
elseif($act == 'del')
{
    $id = intval($_INPUT['id']);
    if($id <1)js_pop_msg("非法操作");
    $info = $version_open_obj->del_info($id);
    if($info) js_pop_msg('删除数据成功','','version_open.php?act=list');
     js_pop_msg('删除数据失败','','version_open.php?act=list');

}

$tpl->assign('act','insert');
$tpl->output();










