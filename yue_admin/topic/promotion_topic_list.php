<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("promotion_topic_list.tpl.htm");

$page_obj = new show_page ();

$topic_obj = POCO::singleton('pai_topic_class');


$title = urldecode($_INPUT['title']);



$where = 1;



$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
$type_name_list = $type_obj -> get_type_attribute_cate(0);
foreach($type_name_list as $val)
{
    $type_name[$val['id']] = $val;
}


$show_count = 40;
$page_obj->setvar (array("title"=>$title) );

$total_count = $topic_obj->get_promotion_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $topic_obj->get_promotion_list(false, $where, 'add_time DESC', $page_obj->limit());

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
	$list[$k]['begin_time'] = date("Y-m-d H:i",$val['begin_time']);
	$list[$k]['end_time'] = date("Y-m-d H:i",$val['end_time']);
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('list', $list);
$tpl->assign('title', $title);
$tpl->assign('topic_list', $topic_list);
$tpl->assign('topic_id', $topic_id);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();
?>