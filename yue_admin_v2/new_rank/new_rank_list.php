<?php
/**
 * @desc:   榜单列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/2
 * @Time:   11:04
 * version: 1.0
 */
include_once('rank_common.inc.php');
$module_list = include_once('module_onfig.inc.php'); //模板名
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
$cms_rank_obj = new pai_cms_rank_class();//榜单类
$rank_event_v3_obj = POCO::singleton( 'pai_rank_event_v3_class' );//接口榜单类


$page_obj = new show_page();
$show_page = 20;
$tpl = new SmartTemplate( CMS_RANK_TEMPLATES_ROOT.'new_rank_list.tpl.htm' );

$act = trim($_INPUT['act']);

$page_type = trim($_INPUT['page_type']);
//$module_type = trim($_INPUT['module_type']);
$type_id = intval($_INPUT['type_id']);
$versions_id = intval($_INPUT['versions_id']);
$switch = trim($_INPUT['switch']);
$add_user_id = intval($_INPUT['add_user_id']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$province = intval($_INPUT['province']);
$location_id = intval($_INPUT['location_id']);
$title = trim($_INPUT['title']);

//条件开始
$where_str = '';
$setParam = array();

if(strlen($page_type) >0) $setParam['page_type'] = $page_type;
if($type_id >0)
{
    foreach($type_list as $k => &$v)//分类
    {
        $v['selected'] = $type_id==$v['type_id'] ? true : false;
    }
    $setParam['type_id'] = $type_id;
}
if($versions_id >0)
{
    foreach($versions_list as &$val)
    {
        $val['selected'] = $versions_id==$val['versions_id'] ? true : false;
    }
    $setParam['versions_id'] = $versions_id;
}
if(strlen($switch) >0) $setParam['switch']=$switch;
if($add_user_id >0) $setParam['add_user_id'] = $add_user_id;
if(strlen($start_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(strlen($end_date)>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
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
if(strlen($title)>0) //标题
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "title LIKE '%".mysql_escape_string($title)."%'";
    $setParam['title'] = $title;
}

$page_obj->setvar($setParam);
$total_count = $cms_rank_obj->get_main_rank_list(true,$page_type,$type_id,$versions_id,$switch,$add_user_id,$where_str);

$page_obj->set($show_page,$total_count);

$list = $cms_rank_obj->get_main_rank_list(false,$page_type,$type_id,$versions_id,$switch,$add_user_id,$where_str,'switch ASC,`order` DESC,`id` DESC,`add_time` DESC', $page_obj->limit());

if(!is_array($list)) $list = array();

foreach($list as &$val)
{
    $val['145_img'] = $val['thumb_img'] = yueyue_resize_act_img_url($val['img_url']);
    $val['city'] = get_poco_location_name_by_location_id($val['location_id']);
    $val['versions_name'] = $cms_rank_obj->get_versions_name_by_id($val['versions_id']);
    if($val['type'] == 0) $val['url_cut']  = poco_cutstr($val['link'],20,'...');
    $val['remark_cut'] = poco_cutstr($val['remark'],20,'...');
    $val['info_count'] = $cms_rank_obj->get_rank_info_list(true,$val['id']);
    $val['module_name'] = $cms_rank_obj->get_module_name_by_type($val['module_type']);
    if($val['page_type'] == 'list')
    {
        $val['curl'] = "yueyue://goto?type=inner_app&pid=1220145&query={$val['id']}";
    }else
    {
        $val['curl'] = $rank_event_v3_obj->get_url_by_type($val['type_id'],$val['title'],0,$val['url']);
    }

}

$tpl->assign('total_count',$total_count);
$tpl->assign('versions_list',$versions_list);
$tpl->assign('type_list',$type_list);
$tpl->assign('list',$list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign($setParam);
$tpl->output();