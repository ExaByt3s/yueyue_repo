<?php
ini_set("memory_limit","512M");

include_once 'common.inc.php';
include_once("/disk/data/htdocs232/poco/php_common/sql_err_log/sql_err_log_common.inc.php");

$begin_day = trim($_POST['begin_day']);
$end_day = trim($_POST['end_day']);

if (preg_match("/\d\d\d\d-\d\d-\d\d/", $begin_day) || preg_match("/\d\d\d\d\d\d\d\d/", $begin_day))
{
	$begin_day = str_replace("-", "", $begin_day);
}
else
{
	$begin_day = date("Ymd", time() - 3600 * 24 * 5);		//默认5天以前
}


if (preg_match("/\d\d\d\d-\d\d-\d\d/", $end_day) || preg_match("/\d\d\d\d\d\d\d\d/", $end_day))
{
	$end_day = str_replace("-", "", $end_day);
}
else
{
	$end_day = date("Ymd", time());							//默认今天
}



$err_log = new poco_sql_err_log_class();
$arr_day_list = $err_log->get_err_log_day_list($begin_day, $end_day);



/**
 * 慢查询
 */
$poco_sql_slowquery_log_obj = new poco_sql_slowquery_log_class();
$slowquery_day_list = $poco_sql_slowquery_log_obj->get_slowquery_log_day_list($begin_day, $end_day);



$tpl = new SmartTemplate("err_day_list.tpl.htm");
$tpl->cache_lifetime= 3600*3; //缓存3小时
$tpl->assign("begin_day", $begin_day);
$tpl->assign("end_day", $end_day);
$tpl->assign("arr_day_list", $arr_day_list);
$tpl->assign("slowquery_day_list", $slowquery_day_list);
$tpl->output();
?>