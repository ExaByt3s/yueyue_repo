<?php
/**
 * @desc:   关键字排行列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/26
 * @Time:   15:47
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once('common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_search_keywords_class.inc.php');
$type_list = include_once ('cat_config.inc.php');//分类配置
$search_keywords_obj = new pai_search_keywords_class();

$page_obj = new show_page();
$show_total = 20;

$tpl = new SmartTemplate( TEMPLATES_ROOT.'keywords_list.tpl.htm' );

$act = trim($_INPUT['act']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$type = trim($_INPUT['type']);
$type_id = (int)$_INPUT['type_id'];
$keyword = trim($_INPUT['keyword']);
$province = intval($_INPUT['province']);
$location_id = intval($_INPUT['location_id']);

$where_str = "keyword !=''";
$setParam = array();

if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
}
else
{
    $start_date = date('Y-m-d',time()-3*24*3600);
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
}
else
{
    $end_date = date('Y-m-d',time()-24*3600);
}
if(strtotime($end_date)-strtotime($start_date)>= 30*24*3600) js_pop_msg_v2("超过30天无法查看",true);
if(strlen($start_date)>0) $setParam['start_date'] = $start_date;
if(strlen($end_date)>0) $setParam['end_date'] = $end_date;
if(strlen($type)>0) $setParam['type'] = $type;
if($type_id>0)
{
    foreach($type_list as &$v)
    {
        $v['selected'] = $type_id==$v['type_id'] ? true : false;
    }
    $setParam['type_id'] = $type_id;
}
if(strlen($keyword)>0) $setParam['keyword'] = $keyword;
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

//导出数据
if($act == 'export')
{
    $list =$search_keywords_obj->get_search_keywords_list(false,$start_date,$end_date,$type,$type_id,$keyword,$where_str,"C desc","0,99999999","COUNT(*) AS C,keyword","GROUP BY keyword");
    if(!is_array($list)) $list = array();
    $fileName = '搜索关键字排行列表';
    $headArr  = array("搜索次数","关键字");
    Excel_v2::start($headArr,$list,$fileName);
    exit;
}

$page_obj->setvar($setParam);

$total_count = $search_keywords_obj->get_search_keywords_list(true,$start_date,$end_date,$type,$type_id,$keyword,$where_str,'','',"COUNT(DISTINCT(keyword)) AS C");
$page_obj->set($show_total,$total_count);

$list =$search_keywords_obj->get_search_keywords_list(false,$start_date,$end_date,$type,$type_id,$keyword,$where_str,"C desc",$page_obj->limit(),"keyword,COUNT(*) AS C","GROUP BY keyword");
if(!is_array($list)) $list = array();



$tpl->assign('type_list',$type_list);
$tpl->assign('list',$list);
$tpl->assign($setParam);

$tpl->assign('page',$page_obj->output(true));
$tpl->output();