<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("topic_user_list.tpl.html");

$area_array = array("广州", "北京", "上海", "成都", "南京", "江苏", "湖南", "湖北", "广东", "福建", "辽宁", "黑龙江", "山西", "海南", "天津", "重庆", "内蒙古", "河北", "吉林", "安徽", "山东", "浙江", "江西", "广西", "贵州", "四川", "云南", "陕西", "甘肃", "宁夏", "新疆", "西藏");


$p = $_GET['p']?$_GET['p']:1;
$area = $_GET['area'];
$cup  = $_GET['cup'];

$page_obj = new show_page ();

$show_count = 10;
$page_obj->setvar ( );

$sql_str = "SELECT COUNT(yue_user_id) AS `c` FROM pai_topic_db.pai_topic_user_info_tbl WHERE 1=1 ";
if($area)   $sql_str .= " AND area LIKE '{$area}' ";
if($cup)    $sql_str .= " AND cup = '{$cup}' ";

$result = db_simple_getdata($sql_str, TRUE, 101);
$total_count = $result['c'];

$page_obj->set ( $show_count, $total_count );

$sql_str        = "SELECT L.*, R.cellphone, DATE_FORMAT(FROM_UNIXTIME(R.add_time), '%Y-%m-%d %H:%i:%s') AS add_time 
                FROM pai_topic_db.pai_topic_user_info_tbl AS L, pai_db.pai_user_tbl AS R 
                WHERE L.yue_user_id = R.user_id ";
if($area)   $sql_str .= " AND area LIKE '{$area}' ";
if($cup)    $sql_str .= " AND cup = '{$cup}' ";
$sql_str .= " ORDER BY L.yue_user_id DESC"; 
if($p == 1)
{
    $limit = ' 0,10';
}else{
    $limit = " " . (int)($p-1) * 10 . " ,10";
}
$sql_str .= " LIMIT " . $limit;
$model_list  = db_simple_getdata($sql_str, FALSE, 101);
//$model_list = $model_card_obj->get_model_card_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $page_obj->limit(), $fields = '*');

if(!$area)   $area = '全国';
if(!$cup)    $cup = '全部';

$area_str = "";
foreach($area_array AS $val)
{
    if($val == $area)
    {
        $area_str .= '<option value="' . $val . '" selected >' . $val . '</option>';
    }else{
        $area_str .= '<option value="' . $val . '" >' . $val . '</option>'; 
    }
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign("area", $area);
$tpl->assign($cup, 'selected');
$tpl->assign("area_str", $area_str);
$tpl->assign('list', $model_list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();
?>