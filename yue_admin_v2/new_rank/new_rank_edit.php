<?php
/**
 * @desc:   新榜单修改和添加【大框架哪里】
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/2
 * @Time:   11:10
 * version: 1.0
 */
include_once('rank_common.inc.php');
$module_list = include_once('module_onfig.inc.php'); //模板名
$cms_rank_obj = new pai_cms_rank_class();//榜单类
$tpl = new SmartTemplate( CMS_RANK_TEMPLATES_ROOT.'new_rank_edit.tpl.htm' );
$user_id = intval($yue_login_id);

$act = trim($_INPUT['act']);
$id = intval($_INPUT['id']);
$location_id = intval($_INPUT['location_id']);
$page_type = trim($_INPUT['page_type']);//所属地方
$type_id = intval($_INPUT['type_id']);
$versions_id = intval($_INPUT['versions_id']);//版本号
$link = trim($_INPUT['link']);//链接
$title = trim($_INPUT['title']);//标题
$img_url = trim($_INPUT['img_url']);//图片链接
$remark = trim($_INPUT['remark']);//备注
$switch = trim($_INPUT['switch']);//开关
$order = intval($_INPUT['order']);

$setParam = array('act'=> 'insert');

//添加log
$cms_rank_obj->add_info_and_log($id,0,$act);

if($act == 'insert')//插入
{
    if(strlen($link)>0) $link = $cms_rank_obj->trimall($link);//过滤空格
    if($location_id <1) js_pop_msg_v2('城市ID不能为空');
    if(strlen($page_type) <1)  js_pop_msg_v2('所属位置不能为空');
    if(strlen($module_type)<1) js_pop_msg_v2('模板不能为空');
    if(strlen($title) <1) js_pop_msg_v2('标题不能为空');
    if($page_type == 'list' || $page_type == 'category_index')
    {
        if($type_id <1) js_pop_msg_v2('分类ID不能为空');
    }
    else
    {
        $type_id = 0;
    }//不是列表页时type_id不要
    $ret = $cms_rank_obj->create_main_rank($location_id, $page_type, $module_type,$type_id,$versions_id,$title, $order,$link, $img_url,$remark,$switch);
    print_r($ret);
    $retID = intval($ret['code']);
   if($retID >0) js_pop_msg_v2('添加成功',false,"new_rank_list.php");
    js_pop_msg_v2('添加失败');

}
elseif($act == 'edit')//修改
{
    if($id <1) js_pop_msg_v2('非法操作');
    $ret = $cms_rank_obj->get_main_info_by_id($id);
    foreach($type_list as $k => &$v)//分类
    {
        $v['selected'] = $ret['type_id']==$v['type_id'] ? true : false;
    }
    foreach($versions_list as &$val)
    {
        $val['selected'] = $ret['versions_id']==$val['versions_id'] ? true : false;
    }
    foreach($module_list as &$mv)
    {
        $mv['selected'] = $ret['module_type']==$mv['module_type'] ? true : false;
    }
    $ret['province']  = substr($ret['location_id'],0,6);
    $setParam['rank_id'] = intval($ret['rank_id']);
    $setParam['act'] = 'update';
    $tpl->assign($ret);
}
elseif($act == 'update') //更新
{
    if(strlen($link)>0) $link = $cms_rank_obj->trimall($link);//过滤空格
    if($id <1) js_pop_msg_v2('非法操作');
    if(strlen($page_type) <1)  js_pop_msg_v2('所属位置不能为空');
    if(strlen($module_type)<1) js_pop_msg_v2('模板不能为空');
    if($page_type == 'list' || $page_type == 'category_index')
    {
        if($type_id <1) js_pop_msg_v2('分类ID不能为空');
    }
    else
    {
        $type_id = 0;
    }//不是列表页时type_id不要
    if(strlen($title) <1) js_pop_msg_v2('标题不能为空');
    $ret = $cms_rank_obj-> update_main_rank_info($id,$location_id, $page_type, $module_type,$type_id,$versions_id, $title, $order, $link, $img_url,$remark,$switch);
    $retID = intval($ret['code']);
    /*if($yue_login_id == 100293)
    {
        if($retID>0) js_pop_msg_v2('更新成功',false,"?act=edit&id={$id}");
    }*/
    if($retID>0) js_pop_msg_v2('更新成功',false,"?act=edit&id={$id}");
    js_pop_msg_v2('更新失败');
}
elseif($act == 'del')//删除
{
    if($id <1) js_pop_msg_v2('非法操作');
    $ret = $cms_rank_obj->get_rank_info_by_main_id($id);
    if(is_array($ret) && !empty($ret))
    {
        echo "<script type='text/javascript'>window.alert('删除失败，榜单存在下属，请删除下属再进行操作！');location.href=document.referrer;</script>";
        exit;
    }
    $del_ret = $cms_rank_obj->del_main_rank($id);
    echo "<script type='text/javascript'>window.alert('删除榜单成功');location.href=document.referrer;</script>";
}
$tpl->assign($setParam);
$tpl->assign('type_list',$type_list);
$tpl->assign('versions_list',$versions_list);
$tpl->assign('module_list',$module_list);
$tpl->assign('user_id',$user_id);
$tpl->assign($setParam);
$tpl->output();