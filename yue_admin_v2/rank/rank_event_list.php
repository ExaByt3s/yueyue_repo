<?php
/**
 * @desc:   首页和内容页配置 列表向上
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/20
 * @Time:   9:04
 * version: 2.0
 */
include('common.inc.php');
$cat_list = include('cat_config.inc.php');//分类配置
$versions_list = include('versions_config.inc.php');//版本配置
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
$rank_event_v2_obj = POCO::singleton('pai_rank_event_v2_class');
include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");//频道
$cms_db_obj             = POCO::singleton ( 'cms_db_class' );
$cms_system_obj         = POCO::singleton ( 'cms_system_class' );
$page_obj               = new show_page ();
$show_count             = 20;
$tpl = new SmartTemplate("rank_event_list.tpl.htm");

$act = trim($_INPUT['act']) ;
$versions_id = intval($_INPUT['versions_id']);
$place   = trim($_INPUT['place']);
$cat_id = intval($_INPUT['cat_id']);
$start_add_time = trim($_INPUT['start_add_time']);
$end_add_time = trim($_INPUT['end_add_time']);
$add_id = intval($_INPUT['add_id']);
$province = intval($_INPUT['province']);
$location_id = intval($_INPUT['location_id']);
$status = isset($_INPUT['status']) ? intval($_INPUT['status']) : -1;
//初始化
$where_str = '';
$setParam = array();

if($versions_id>0)
{
    $setParam['versions_id'] = $versions_id;
}
if(strlen($place)>0)
{
    $setParam['place'] = $place;
}
if($cat_id >0)
{
    foreach($cat_list as $k => &$v2)//分类
    {
        $v2['selected'] = $cat_id==$v2['cat_id'] ? true : false;
    }
    $setParam['cat_id'] = $cat_id;
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "cat_id={$cat_id}";
}
if($province >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    if($location_id >0)
    {
        $where_str .= "location_id={$location_id}";
        $setParam['location_id'] = $location_id;
    }
    else
    {
        $where_str .= "LEFT(location_id,6)={$province}";
    }
    $setParam['province'] = $province;
}
if($status >=0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "status={$status}";
    $setParam['status'] = $status;
}
if(strlen($start_add_time) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') >='".mysql_escape_string($start_add_time)."'";
    $setParam['start_add_time']  = $start_add_time;
}
if(strlen($end_add_time) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') <='".mysql_escape_string($end_add_time)."'";
    $setParam['end_add_time']  = $end_add_time;
}
if($add_id >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "add_id={$add_id}";
    $setParam['add_id'] = $add_id;
}
$total_count = $rank_event_v2_obj->get_rank_event_list(true,$versions_id,$place,0,$where_str);
$page_obj->setvar($setParam);
$page_obj->set($show_count,$total_count);
$list = $rank_event_v2_obj->get_rank_event_list(false,$versions_id,$place,0,$where_str,'sort_order DESC,id DESC',$page_obj->limit());
foreach($list as $key=>&$val)
{
    if($val['type_id'] == 0) $val['url_cut']  = poco_cutstr($val['url'],20,'...');
    $val['headtile_cut']  = poco_cutstr($val['headtile'],20,'...');
    $val['subtitle_cut']  = poco_cutstr($val['subtitle'],20,'...');
    $val['rank_desc_cut']  = poco_cutstr($val['rank_desc'],20,'...');
    $val['versions_name'] = $rank_event_v2_obj->versions_option($val['versions_id']);
    $val['city'] = get_poco_location_name_by_location_id($val['location_id']);
    //$val['145_img'] = yueyue_resize_act_img_url($val['cover_url'],145);
    $val['145_img'] = $val['thumb_img'] = yueyue_resize_act_img_url($val['cover_url']);
    $rank_info = $cms_system_obj->get_rank_info_by_rank_id($val['rank_id']);
    $val['rank_name'] = $rank_info['rank_name'];
    $val['curl'] =  $rank_event_v2_obj->get_url_by_type($val['type'],$val['headtile'],$val['pid'],$val['url'],$val['rank_id'],$val['cms_type']);
}

foreach($versions_list as &$v)
{
    $v['selected'] = $v['versions_id']==$versions_id ? true : false;
}

$tpl->assign('cat_list',$cat_list);
$tpl->assign('versions_list',$versions_list);
$tpl->assign('list',$list);
$tpl->assign('total_count',$total_count);
$tpl->assign($setParam);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
