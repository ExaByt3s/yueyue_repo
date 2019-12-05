<?php
/**
 * @desc:   评价结果统计
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/31
 * @Time:   17:50
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once('common.inc.php');
check_auth($yue_login_id,'mall_comment_for_buyer');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$mall_comment_log_obj = POCO::singleton( 'pai_mall_comment_log_class' );//评价类
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类

$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT.'mall_comment_for_buyer_list.tpl.htm');

$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']);

$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$type_id = intval($_INPUT['type_id']);

$where_str = '';
$setParam = array();

//商品品类选择
$type_list = $type_obj->get_type_cate(2);

if(!preg_match("/\d\d\d\d-\d\d/", $month))
{
    $month = date('Y-m',time()-24*3600);
}
if(strlen($month) >0)
{
    $date = $month.'-01';
    $setParam['month'] = $month;
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m')='".mysql_escape_string($month)."'";
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',$end_date);
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if($type_id >0)
{
    $setParam['type_id'] = $type_id;
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
}

$list = $mall_comment_log_obj->get_comment_for_buyer_list(false,$date,$type_id,$where_str,"GROUP BY FROM_UNIXTIME(add_time,'%Y-%m-%d')" ,'add_time DESC','0,31',"FROM_UNIXTIME(add_time,'%Y-%m-%d') AS add_time,count(*) AS over_score_count,SUM(CASE WHEN overall_score=1 THEN 1 ELSE 0 END) AS overall_score_1
,SUM(CASE WHEN overall_score=2 THEN 1 ELSE 0 END) AS overall_score_2,SUM(CASE WHEN overall_score=3 THEN 1 ELSE 0 END) AS overall_score_3
,SUM(CASE WHEN overall_score=4 THEN 1 ELSE 0 END) AS overall_score_4,SUM(CASE WHEN overall_score=5 THEN 1 ELSE 0 END) AS overall_score_5");
if(!is_array($list)) $list = array();

foreach($list as &$v)
{
   $v['scale_overall_score_1'] = sprintf('%.2f',($v['overall_score_1']/$v['over_score_count'])*100).'%';
   $v['scale_overall_score_2'] = sprintf('%.2f',($v['overall_score_2']/$v['over_score_count'])*100).'%';
   $v['scale_overall_score_3'] = sprintf('%.2f',($v['overall_score_3']/$v['over_score_count'])*100).'%';
   $v['scale_overall_score_4'] = sprintf('%.2f',($v['overall_score_4']/$v['over_score_count'])*100).'%';
   $v['scale_overall_score_5'] = sprintf('%.2f',($v['overall_score_5']/$v['over_score_count'])*100).'%';
}

if($act == 'export')//导出数据
{
    $data = array();
    foreach($list as $key=>$val)
    {
        $data[$key]['add_time'] = $val['add_time'];
        $data[$key]['overall_score_1'] = $val['overall_score_1'];
        $data[$key]['scale_overall_score_1'] = $val['scale_overall_score_1'];
        $data[$key]['overall_score_2'] = $val['overall_score_2'];
        $data[$key]['scale_overall_score_2'] = $val['scale_overall_score_2'];
        $data[$key]['overall_score_3'] = $val['overall_score_3'];
        $data[$key]['scale_overall_score_3'] = $val['scale_overall_score_3'];
        $data[$key]['overall_score_4'] = $val['overall_score_4'];
        $data[$key]['scale_overall_score_4'] = $val['scale_overall_score_4'];
        $data[$key]['overall_score_5'] = $val['overall_score_5'];
        $data[$key]['scale_overall_score_5'] = $val['scale_overall_score_5'];
        $data[$key]['over_score_count'] = $val['over_score_count'];
    }
    $fileName = '评价结果列表';
    $headArr  = array("日期","评分为1数量","评分为1比例","评分为2数量","评分为2比例","评分为3数量","评分为3比例","评分为4数量","评分为4比例","评分为5数量","评分为5比例","总评条数");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$tpl->assign($setParam);
$tpl->assign('type_list',$type_list);
$tpl->assign('list',$list);
$tpl->output();

