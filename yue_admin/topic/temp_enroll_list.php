<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("temp_enroll_list.tpl.htm");

$page_obj = new show_page ();

$topic_obj = POCO::singleton('pai_topic_class');


$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];

$where = 1;

if($begin_time && $end_time)
{
    $bt = strtotime($begin_time);
    $et = strtotime($end_time)+86400;
    $where .= " AND add_time BETWEEN $bt AND $et";
}

$show_count = 40;

$page_obj->setvar (array("begin_time"=>$begin_time,"end_time"=>$end_time) );

$total_count = $topic_obj->get_temp_enroll_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $topic_obj->get_temp_enroll_list(false, $where, 'add_time DESC', $page_obj->limit());

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('list', $list);
$tpl->assign('begin_time', $begin_time);
$tpl->assign('end_time', $end_time);

$tpl->output();
?>