<?php
/**
 * @desc:   次榜单数据修改
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/2
 * @Time:   17:09
 * version: 1.0
 */
include_once('rank_common.inc.php');
$cms_rank_obj = new pai_cms_rank_class();//榜单类
include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");//频道
$cms_db_obj             = POCO::singleton ( 'cms_db_class' );
$cms_system_obj         = POCO::singleton ( 'cms_system_class' );
$rank_event_v2_obj = POCO::singleton( 'pai_rank_event_v2_class' );

$tpl = new SmartTemplate( CMS_RANK_TEMPLATES_ROOT.'new_rank_info_edit_v2.tpl.htm' );

$selected = "selected='true'";
$channel_list = $cms_db_obj->get_cms_list("channel_tbl");
$user_id = intval($yue_login_id);

$act = trim($_INPUT['act']);
$id = intval($_INPUT['id']);
$main_id = intval($_INPUT['main_id']);
$type = trim($_INPUT['type']);
$rank_type = intval($_INPUT['rank_type']);
$rank_id = intval($_INPUT['rank_id']);
$cms_type = trim($_INPUT['cms_type']);//榜单类型
$pid = intval($_INPUT['pid']);
$title = trim($_INPUT['title']);
$content = trim($_INPUT['content']);
$img_url = trim($_INPUT['img_url']);
$link_url = trim($_INPUT['link_url']);
$remark = trim($_INPUT['remark']);
$order = intval($_INPUT['order']);
$switch = $_INPUT['switch'];
$setParam = array('act'=> 'insert');


$cms_rank_obj->add_info_and_log($main_id,$id,$act);
if($act == 'insert')//插入
{
    if(strlen($link_url)>0) $link_url = $cms_rank_obj->trimall($link_url);//过滤空格
    if($main_id <1) js_pop_msg_v2('主ID不能为空');
    //if(strlen($type) <1) js_pop_msg_v2('分类不能为空');
    if(strlen($title) <1) js_pop_msg_v2('标题不能为空');
    if($pid <1) js_pop_msg_v2('模板ID不能为空');
    if($rank_type == 1)//榜单方式
    {
        if($rank_id <1) js_pop_msg_v2('榜单ID不能为空');
        if(strlen($cms_type) <1) js_pop_msg_v2('榜单类型不能为空');
    }else
    {
        if(strlen($link_url) <1) js_pop_msg_v2('链接不能为空');
        /*if($pid >0)
        {
            $str_prev = "/&pid=([a-zA-Z0-9])+&/i";
            $link_url = preg_replace($str_prev,"&pid={$pid}&", $link_url);
        }*/
        $rank_id = 0;
        $cms_type = '';

    }
    $ret = $cms_rank_obj->create_info_rank($main_id, $type, $rank_type,$rank_id,$cms_type,$pid,$title, $content, $img_url, $link_url, $remark,$order,$switch);
    $retID = intval($ret['code']);
    if($retID >0) js_pop_msg_v2('添加成功',false,'new_rank_info_list.php?main_id='.$main_id);
    js_pop_msg_v2('添加失败');
}
elseif($act == 'edit')//修改
{
    if($id <1) js_pop_msg_v2('非法操作');
    $ret = $cms_rank_obj->get_rank_info_by_id($id);
    $setParam['act'] = 'update';
    $setParam['rank_id'] = $ret['rank_id'];
    $tpl->assign($ret);
}
elseif($act == 'update')//更新
{
    if(strlen($link_url)>0) $link_url = $cms_rank_obj->trimall($link_url);//过滤空格
    if($id <1) js_pop_msg_v2('非法操作');
    //if(strlen($type) <1) js_pop_msg_v2('分类不能为空');
    if(strlen($title) <1) js_pop_msg_v2('标题不能为空');
    if($pid <1) js_pop_msg_v2('模板ID不能为空');
    if($rank_type == 1)//榜单方式
    {
        if($rank_id <1) js_pop_msg_v2('榜单ID不能为空');
        if(strlen($cms_type) <1) js_pop_msg_v2('榜单类型不能为空');
        $link_url = '';
    }else
    {
        if(strlen($link_url) <1) js_pop_msg_v2('链接不能为空');
        /*if($pid >0)
        {
            $str_prev = "/&pid=(1220101|1220122|1220128)+&/i";
            $link_url = preg_replace($str_prev,"&pid={$pid}&", $link_url);
        }*/
        $rank_id = 0;
        $cms_type = '';

    }
    $ret = $cms_rank_obj->update_info_rank_info($id,$main_id, $type, $rank_type,$rank_id,$cms_type,$pid, $title, $content, $img_url, $link_url, $remark,$order,$switch);
    $retID = intval($ret['code']);
    if($retID >0) js_pop_msg_v2('修改成功',false,'new_rank_info_list.php?main_id='.$main_id);
    js_pop_msg_v2('添加失败');
}
elseif($act == 'del')//删除
{
    if($id <1) js_pop_msg_v2('非法操作');
    $retId = $cms_rank_obj->del_info_rank($id);
    $retId = intval($retId);
    if($retId >0)
    {
        echo "<script type='text/javascript'>window.alert('删除榜单下属榜单成功');location.href=document.referrer;</script>";
        exit;
    }
    echo "<script type='text/javascript'>window.alert('删除榜单下属榜单失败');location.href=document.referrer;</script>";
    exit;
}
elseif ($act == 'rank')//显示榜单内容ajax获取
{
    $channel_id = intval($_INPUT['channel_id']);
    if ($channel_id <1)
    {
        echo 0;
    }
    $channel_id > 0 && $where = 'channel_id = ' . $channel_id;
    $rank_list = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");//取榜单
    if ($rank_list)
    {
        foreach ($rank_list as $key => $vo)
        {
            $rank_list[$key]['rank_name'] = iconv("GBK", "UTF-8" , $vo['rank_name']);
        }
    }
    $arr  = array
    (
        'msg' => 'success' ,
        'ret' => $rank_list
    );
    echo json_encode($arr);
    exit;
}

$ret_info = $cms_rank_obj->get_main_info_by_id($main_id);
if(is_array($ret_info))
{
    //$setParam['main_id'] = $ret_info['id'];
    $setParam['page_type'] = $ret_info['page_type'];
    $setParam['page_title'] = $ret_info['title'];
    $setParam['module_type'] = $ret_info['module_type'];
}

$rank_info = $cms_system_obj->get_rank_info_by_rank_id($setParam['rank_id']);//榜单
if ($rank_info)
{
    $channel_id = $rank_info['channel_id'];
    foreach ($channel_list as $key => $vo)
    {
        if ($vo['channel_id'] == $channel_id)
        {
            $channel_list[$key]['channel_selected'] = $selected;
        }
    }
    $channel_id > 0 && $where = 'channel_id = ' . $channel_id;
    $rank_list = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");
    //取榜单
    if ($rank_list)
    {
        foreach ($rank_list as $rank_key => $rank_vo)
        {
            if ($rank_vo['rank_id'] == $setParam['rank_id'])
            {
                $rank_list[$rank_key]['rank_selected'] = $selected;
            }
        }
    }
}
$tpl->assign('channel_list', $channel_list);
$tpl->assign('rank_list', $rank_list);
if($main_id <1) js_pop_msg_v2('主ID不能为空,非法操作');
$tpl->assign('main_id',$main_id);
$tpl->assign($setParam);
$tpl->assign('main_list',$main_list);
$tpl->assign('user_id',$user_id);
$tpl->output();