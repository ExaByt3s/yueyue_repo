<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("topic_list.tpl.htm");

$page_obj = new show_page ();

$topic_obj = POCO::singleton('pai_topic_class');


$title = urldecode($_INPUT['title']);

if($_GET['act'] == 'del')
{
    $id = $_GET['id'];
    if($topic_obj->del_topic($id))
    {
        echo "<script>alert('删除成功')</script>";
    }
}

if($_GET['act'] == 'effect_on')
{
    $id = $_GET['id'];
    $topic_obj->update_effect($id, 1);
    echo "<script>alert('上架成功')</script>";

}

if($_GET['act'] == 'effect_off')
{
    $id = $_GET['id'];
    $topic_obj->update_effect($id, 0);
    echo "<script>alert('下架成功')</script>";

}

$where = 1;

if($title)
{
    $where .= " AND title LIKE '%{$title}%'";
}

$show_count = 40;
$page_obj->setvar (array("title"=>$title) );

$total_count = $topic_obj->get_topic_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $topic_obj->get_topic_list(false, $where, 'is_effect DESC, add_time DESC,sort DESC', $page_obj->limit());

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
    $list[$k]['nickname'] = get_user_nickname_by_user_id($val['author_id']);
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('list', $list);
$tpl->assign('title', $title);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();
?>