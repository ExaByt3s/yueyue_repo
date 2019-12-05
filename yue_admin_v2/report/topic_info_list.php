<?php
/**
 * @desc:   专题下单列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/11
 * @Time:   15:11
 * version: 1.0
 */
include_once('common.inc.php');
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_topic_report_class.inc.php');
check_auth($yue_login_id,'topic_info_list');//权限控制
$topic_report_obj = new pai_topic_report_class();
$page_obj = new show_page();
$show_total = 20;
$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT."topic_info_list.tpl.htm");

$act = trim($_INPUT['act']);
$topic_id = (int)$_INPUT['topic_id'];
$date_time = trim($_INPUT['date_time']);


if($topic_id <1) js_pop_msg_v2('非法操作');
if(!preg_match("/\d\d\d\d-\d\d-\d\d/", $date_time) && !preg_match("/\d\d\d\d\d\d\d\d/", $date_time)) js_pop_msg_v2('非法操作');

$where_str = '';
$setParam = array();

if($topic_id >0) $setParam['topic_id'] = $topic_id;
if(strlen($date_time)>0)
{
    $date_time = date('Y-m-d',strtotime($date_time));
    $setParam['date_time'] = $date_time;
}

$page_obj->setvar($setParam);
$total_count = $topic_report_obj->get_topic_info_list(true,$topic_id,$date_time,$where_str);
$page_obj->set($show_total,$total_count);
$list = $topic_report_obj->get_topic_info_list(false,$topic_id,$date_time,$where_str,"date_time DESC,id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();

$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();
