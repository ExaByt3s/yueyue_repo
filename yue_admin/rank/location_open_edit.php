<?php
/**
 * @desc:   地区修改和增加控制器
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/9
 * @Time:   13:36
 * version: 1.0
 */

include('common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php');

$location_open_obj = POCO::singleton('pai_location_open_class');
$version_open_obj = POCO::singleton('pai_version_open_class');

$tpl = new SmartTemplate("location_open_edit.tpl.htm");

$act = trim($_INPUT['act']);

if($act == 'insert')   //插入
{
    $location_id = intval($_INPUT['location_id']);
    $version_id  = intval($_INPUT['version_id']);

    if($location_id <1) js_pop_msg('城市ID不能为空');
    if($version_id <1) js_pop_msg('版本ID不能为空');
    $info = $location_open_obj->add_info($location_id,$version_id);

    if($info) js_pop_msg("添加成功");

    js_pop_msg("添加失败,可能该版本的地区已经存在");
}
elseif($act == 'edit') //修改
{
    $id = intval($_INPUT['id']);
    if($id <1) js_pop_msg("非法操作");
    $ret =  $location_open_obj->get_info($id);

    //获取location_id省级数据
    $prov_id = $province = substr($ret['location_id'], 0,6);
    $prov_ret = location_format($arr_locate_a,$prov_id);

    //地区数据
    $location_ret = ${'arr_locate_b'.$prov_id};
    $location_ret = location_format($location_ret,$ret['location_id']);

    //版本
    $version_ret = $version_open_obj->get_list(false,'','',0,'','id DESC','0,99999999','version_num,id');
    $version_ret = format_selcted($version_ret,'id',$ret['version_id']);
    $tpl->assign('prov_ret',$prov_ret);//省数据
    $tpl->assign('location_ret',$location_ret);//省数据
    $tpl->assign('version_ret',$version_ret);//省数据
    $tpl->assign($ret);
    $tpl->assign('act','update');
    $tpl->output();
    exit;
}
elseif($act == 'update') //更新
{
    $id = intval($_INPUT['id']);
    $location_id = intval($_INPUT['location_id']);
    $version_id  = intval($_INPUT['version_id']);
    if($id <1)  js_pop_msg("非法操作");
    if($location_id <1) js_pop_msg("城市ID必须大于0");
    if($version_id <1)  js_pop_msg("版本ID不能为空");
    $info = $location_open_obj->update_info($id,$location_id,$version_id);
    if($info) js_pop_msg("更新成功");
    js_pop_msg("更新成功");
}
elseif($act == 'del') //删除
{
    $id = intval($_INPUT['id']);
    if($id <1) js_pop_msg("非法操作");
    $info = $location_open_obj->del_info($id);

    if($info) js_pop_msg("删除成功",'',"location_open.php");
    js_pop_msg("删除失败",'',"location_open.php");
}


//版本数据
$version_ret = $version_open_obj->get_list(false,'','',0,'','id DESC','0,99999999','version_num,id');
$prov_ret = location_format($arr_locate_a);

$tpl->assign('version_ret',$version_ret);
$tpl->assign('prov_ret',$prov_ret);
$tpl->assign('act','insert');
$tpl->output();


/**
 * 把一维数组中的省市数据转化为二维数组
 *
 * @param array $ret  一维数组的省数据
 * @param int $location_id
 * @return array
 */
function location_format($ret = array(),$location_id = 0)
{
    $ret_arr = array();
    $location_id = intval($location_id);
    if(!is_array($ret)) $ret = array();
    $i = 0;
    foreach($ret as $key=>$val)
    {
       if($location_id !=0 && $key == $location_id) $ret_arr[$i]['select'] = "selected='true'";//选择了
       $ret_arr[$i]['id']   = $key;
       $ret_arr[$i]['name'] = $val;
       $i ++;
    }
    return $ret_arr;
}

/**
 * 根据一个数组的下标和相同的比较相同时则选择
 * @param array  $arr
 * @param string $selKey  数组下标
 * @param string $selVal  值
 * @return array
 */
function format_selcted($arr,$selKey,$selVal)
{
    if(!is_array($arr)) $arr = array();
    foreach($arr as $key=>$val)
    {
        if($val[$selKey] == $selVal) $arr[$key]['select'] = "selected='true'";
    }
    return $arr;
}