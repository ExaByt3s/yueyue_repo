<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate("order_list.tpl.htm");

$page_obj = new show_page ();

$channel_oid = $_INPUT['channel_oid'];
$channel_cate = $_INPUT['channel_cate'];
$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];
$goods_id = $_INPUT['goods_id'];

$where = "where 1";

if($channel_oid)
{
    $where .= " AND channel_oid='{$channel_oid}'";
}

if($channel_cate)
{
    $where .= " AND channel_cate='{$channel_cate}'";
}

if($goods_id)
{
    $where .= " AND goods_id='{$goods_id}'";
}

if($begin_time && $end_time)
{
    $where .= " AND paid_date between '{$begin_time}' AND '{$end_time}'";
}

$sql = "SELECT count(*) as c FROM pai_finance_db.pai_report_order_tbl  {$where}";
$count_list = db_simple_getdata($sql,true);

$show_count = 40;

$page_obj->setvar (array("channel_oid"=>$channel_oid) );

$total_count = $count_list['c'];

$page_obj->set ( $show_count, $total_count );

$sql = "SELECT * FROM pai_finance_db.pai_report_order_tbl {$where} ORDER BY paid_date DESC limit ".$page_obj->limit();
$list = db_simple_getdata($sql);



$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign('list', $list);
$tpl->assign('begin_time', $begin_time);
$tpl->assign('end_time', $end_time);
$tpl->assign('channel_oid', $channel_oid);
$tpl->assign('goods_id', $goods_id);
$tpl->assign('channel_cate', $channel_cate);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();

?>