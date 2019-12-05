<?php
/**
 * @desc:   已经下架的商家
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/29
 * @Time:   11:43
 * version: 1.0
 */
include_once('rank_common.inc.php');
$cms_rank_obj = new pai_cms_rank_class();//榜单类
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$page_obj = new show_page();
$show_page = 20;
$tpl = new SmartTemplate( CMS_RANK_TEMPLATES_ROOT.'rank_out_shelf_list.tpl.htm' );

$act = trim($_INPUT['act']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$status = isset($_INPUT['status']) ? intval($_INPUT['status']) : -1;
$province = intval($_INPUT['province']);
$location_id = intval($_INPUT['location_id']);

$where_str = '';
$setParam = array();

$retUrl = $_SERVER['HTTP_REFERER'];

function new_rank_msg_v2($msg,$b_reload = false,$url=NULL)
{
    echo "<script language='javascript'>alert('{$msg}');";
    if($url) echo "location.href = '{$url}';";
    if($b_reload) echo "history.back();";
    echo "</script>";
    exit;
}

if ($act == 'chuli') //恢复数据
{
    $id = intval($_INPUT['id']);
    if ($id <1) new_rank_msg_v2('非法操作',false,$retUrl);
    $code = $cms_rank_obj->update_out_shelf($id,array('status'=>1));
    if($code) new_rank_msg_v2('处理成功',false,$retUrl);
    new_rank_msg_v2('处理失败',false,$retUrl);
    exit;
}


if($status >=0) $setParam['status'] = $status;
if(strlen($start_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(strlen($end_date)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
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

$page_obj->setvar($setParam);
$total_count = $cms_rank_obj->get_rank_out_shelf_list(true,$status,$where_str);
$page_obj->set($show_page,$total_count);

$list = $cms_rank_obj->get_rank_out_shelf_list(false,$status,$where_str,'add_time DESC,id DESC', $page_obj->limit());

if(!is_array($list)) $list = array();
foreach($list  as &$val)
{
    $ret = $cms_rank_obj->get_main_info_by_id($val['main_id']);
    $val['main_name'] = $ret['title'];
    $val['location_name'] = get_poco_location_name_by_location_id($val['location_id']);
    $rank_info = $cms_rank_obj->get_rank_info_by_id($val['rank_id']);
    $val['rank_name'] = $rank_info['title'];
    unset($rank_info);
}

$tpl->assign($setParam);
$tpl->assign('total_count',$total_count);
$tpl->assign('list',$list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign($setParam);
$tpl->output();