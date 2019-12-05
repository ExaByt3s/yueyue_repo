<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate("category_overall.tpl.htm");

$page_obj = new show_page ();

$month = $_INPUT['month'];

if($month)
{
    $where = "WHERE FROM_UNIXTIME(paid_time,'%Y-%m')='{$month}'";

    $select_type = "FROM_UNIXTIME(paid_time,'%Y-%m-%d')";
    $chart_select = "{$select_type} AS `date`";
    $max_text = "最高日";
}
else
{
    $select_type = "FROM_UNIXTIME(paid_time,'%Y-%m')";
    $chart_select = "{$select_type} AS `date`";
    $max_text = "最高月";
}





$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();

?>