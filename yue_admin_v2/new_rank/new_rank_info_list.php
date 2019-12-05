<?php
/**
 * @desc:   榜单下属列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/2
 * @Time:   16:57
 * version: 1.0
 */
include_once('rank_common.inc.php');
$cms_rank_obj = new pai_cms_rank_class();//榜单类
include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");//频道
$cms_db_obj             = POCO::singleton ( 'cms_db_class' );
$cms_system_obj         = POCO::singleton ( 'cms_system_class' );
$page_obj = new show_page();
$show_page = 20;
$tpl = new SmartTemplate( CMS_RANK_TEMPLATES_ROOT.'new_rank_info_list.tpl.htm' );

$act = trim($_INPUT['act']);
$main_id = intval($_INPUT['main_id']);
$type = trim($_INPUT['type']);
$switch = trim($_INPUT['switch']);
$add_user_id = intval($_INPUT['add_user_id']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$title = trim($_INPUT['title']);

$rank_list = $cms_rank_obj->get_main_rank_list(false,'','','','','','','`order` DESC,add_time DESC,id DESC',"0,99999999");

$where_str = '';
$setParam = array();
if($main_id >0)
{
    $setParam['main_id'] = $main_id;
    foreach($rank_list as &$v)
    {
        $v['selected'] = $main_id==$v['id'] ? true : false;
    }
    $ret_info = $cms_rank_obj->get_main_info_by_id($main_id);
    $setParam['page_title'] = $ret_info['title'];
}
if(strlen($type) >0) $setParam['type'] = $type;
if(strlen($switch)>0) $setParam['switch'] = $switch;
if($add_user_id>0) $setParam['add_user_id'] = $add_user_id;
if(strlen($start_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') >='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(strlen($end_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') <='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if(strlen($title)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "title  LIKE '%".mysql_escape_string($title)."%'";
    $setParam['title'] = $title;
}


$page_obj->setvar($setParam);
$total_count = $cms_rank_obj->get_rank_info_list(TRUE,$main_id,$type,$switch,$add_user_id,$where_str);
$page_obj->set($show_page,$total_count);
$list = $cms_rank_obj->get_rank_info_list(false,$main_id,$type,$switch,$add_user_id,$where_str,'`order` DESC,add_time DESC,id DESC', $page_obj->limit());

if(!is_array($list)) $list = array();
foreach($list  as &$val)
{
    $val['145_img'] = $val['thumb_img'] = yueyue_resize_act_img_url($val['img_url']);
    $ret = $cms_rank_obj->get_main_info_by_id($val['main_id']);
    $val['main_name'] = $ret['title'];
    unset($ret);
    if($val['rank_type'] ==1)
    {
        $rank_info = $cms_system_obj->get_rank_info_by_rank_id($val['rank_id']);
        $val['rank_name'] = $rank_info['rank_name'];
        unset($ret_info);
    }else
    {
        $val['url_cut']  = poco_cutstr($val['link_url'],20,'...');
    }
    if(strlen($val['remark']))  $val['remark_cut'] = poco_cutstr($val['remark'],20,'...');
    if(strlen($val['content']))  $val['content_cut'] = poco_cutstr($val['content'],20,'...');
}


foreach($rank_list as &$val)
{
   $val['title'] = poco_cutstr($val['title'],10,'...');
}



$tpl->assign('total_count',$total_count);
$tpl->assign($setParam);
$tpl->assign('rank_list',$rank_list);
$tpl->assign('list',$list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();
