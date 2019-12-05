<?php
/**
 * @desc:   榜单log表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/3
 * @Time:   18:47
 * version: 1.0
 */
include_once('rank_common.inc.php');
$cms_rank_obj = new pai_cms_rank_class();//榜单类
$page_obj = new show_page();
$show_page = 20;
$tpl = new SmartTemplate( CMS_RANK_TEMPLATES_ROOT.'new_rank_log_list.tpl.htm' );

$act = trim($_INPUT['act']);
$start_time = trim($_INPUT['start_time']);
$end_time = trim($_INPUT['end_time']);
$audit_id = intval($_INPUT['audit_id']);
$action   = trim($_INPUT['action']);

$cms_rank_obj->add_info_and_log(0,0,$act);
if ($act == 'restore') //恢复数据
{
    $id = intval($_INPUT['id']);
    if ($id <1)
    {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='new_rank_log_list.php'</script>";
        exit;
    }

    $ret = $cms_rank_obj->restore_new_rank_event_by_id($id,'restore');
    $retID = intval($ret['code']);
    if($retID >0)
    {
        echo "<script type='text/javascript'>window.alert('恢复成功');location.href='new_rank_log_list.php'</script>";
        exit;
    }
    echo "<script type='text/javascript'>window.alert('恢复失败');location.href='new_rank_log_list.php'</script>";
    exit;
}

//查询语句
$where_str = '';
$setParam = array();
if(strlen($start_time) >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(audit_time,'%Y-%m-%d') >='".mysql_escape_string($start_time)."'";
    $setParam['start_time'] = $start_time;
}
if(strlen($end_time)>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(audit_time,'%Y-%m-%d') <='".mysql_escape_string($end_time)."'";
    $setParam['end_time'] = $end_time;
}
if($audit_id >0) $setParam['audit_id'] = $audit_id;
if(strlen($action)>0) $setParam['action'] = $action;
$page_obj->setvar($setParam);
$total_count = $cms_rank_obj->get_rank_cms_log_list(true,$audit_id,$action,$where_str);
$page_obj->set($show_page,$total_count);
$list = $cms_rank_obj->get_rank_cms_log_list(false,$audit_id,$action,$where_str,'id DESC',$page_obj->limit());
if(!is_array($list)) $list = array();
foreach($list as &$val)
{
    $val['nickname'] = get_user_nickname_by_user_id($val['audit_id']);
}

$tpl->assign('list',$list);
$tpl->assign('total_count',$total_count);
$tpl->assign($setParam);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();


