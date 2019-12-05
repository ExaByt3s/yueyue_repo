<?php
/**
 * @desc:   榜单log表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/20
 * @Time:   18:10
 * version: 1.0
 */
include('common.inc.php');
$rank_event_v2_obj     = POCO::singleton('pai_rank_event_v2_class');
$rank_event_log_v2_obj     = POCO::singleton('pai_rank_event_log_v2_class');
$page_obj = new show_page ();
$show_count = 20;
$tpl = new SmartTemplate("rank_event_log_list.tpl.htm");

$act = trim($_INPUT['act']);
$start_time = trim($_INPUT['start_time']);
$end_time = trim($_INPUT['end_time']);
$audit_id = intval($_INPUT['audit_id']);
$action   = trim($_INPUT['action']);

if ($act == 'restore') //恢复数据
{
    $id = intval($_INPUT['id']);
    if ($id <1)
    {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event_log.php'</script>";
        exit;
    }

    $ret = $rank_event_v2_obj->restore_rank_event_by_id($id,'restore');
    $retID = intval($ret['status']);
    if($retID >0)
    {
        echo "<script type='text/javascript'>window.alert('恢复成功');location.href='rank_event_list.php'</script>";
        exit;
    }
    echo "<script type='text/javascript'>window.alert('恢复失败');location.href='rank_event_log.php'</script>";
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
if($audit_id >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "audit_id ={$audit_id}";
    $setParam['audit_id'] = $audit_id;
}
if(strlen($action)>0)//操作
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "act ='".mysql_escape_string($action)."'";
    $setParam['action'] = $action;
}
$total_count = $rank_event_log_v2_obj->get_rank_event_log_list(true, $where_str);
$page_obj->setvar($setParam);
$page_obj->set($show_count,$total_count);
$list = $rank_event_log_v2_obj->get_rank_event_log_list(false, $where_str,'id DESC',$page_obj->limit());
if(!is_array($list)) $list = array();
foreach($list as &$val)
{
   $val['nickname'] = get_user_nickname_by_user_id($val['audit_id']);
}

$tpl->assign('list',$list);
$tpl->assign('total_count',$total_count);
$tpl->assign($setParam);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();