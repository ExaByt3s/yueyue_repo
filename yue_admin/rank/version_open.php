<?php
/**
 * @desc:   版本开通文件
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/8
 * @Time:   11:16
 * version: 1.0
 */

include('common.inc.php');
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$version_open_obj = POCO::singleton('pai_version_open_class');

$tpl = new SmartTemplate("version_open_list.tpl.htm");
$page_obj = new show_page ();
$show_count = 20;


$act = trim($_INPUT['act']);
$start_time = trim($_INPUT['start_time']);
$end_time   = trim($_INPUT['end_time']);
$user_id    = intval($_INPUT['user_id']);

//列表数据开始

$where_str = '';
$setParam  = array();

if(strlen($start_time)>0) $setParam['start_time'] = $start_time;
if(strlen($end_time)>0) $setParam['end_time'] = $end_time;
if($user_id >0) $setParam['user_id'] = $user_id;

$total_count = $version_open_obj->get_list(true, $start_time,$end_time,$user_id,$where_str);

//$pa
$page_obj->set($show_count,$total_count);
$page_obj->setvar($setParam);

$list  = $version_open_obj->get_list(false, $start_time,$end_time,$user_id,$where_str,'id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();

foreach($list as $key=>$val)
{
    $list[$key]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
}

$tpl->assign('total_count', $total_count);
$tpl->assign ( 'list', $list );
$tpl->assign ($setParam);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign ( 'MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER );
$tpl->output ();










