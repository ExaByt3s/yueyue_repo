<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("topic_user_list.tpl.html");

$area_array = array("����", "����", "�Ϻ�", "�ɶ�", "�Ͼ�", "����", "����", "����", "�㶫", "����", "����", "������", "ɽ��", "����", "���", "����", "���ɹ�", "�ӱ�", "����", "����", "ɽ��", "�㽭", "����", "����", "����", "�Ĵ�", "����", "����", "����", "����", "�½�", "����");


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

if(!$area)   $area = 'ȫ��';
if(!$cup)    $cup = 'ȫ��';

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