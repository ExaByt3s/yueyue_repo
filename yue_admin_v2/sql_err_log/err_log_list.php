<?php
ini_set("memory_limit","512M");

include_once 'common.inc.php';
include_once("/disk/data/htdocs232/poco/php_common/sql_err_log/sql_err_log_common.inc.php");

$day = $_GET['day'];
$page = (int)$_GET['page'];
$err_code = (int)$_GET['err_code'];

if ($page < 0)
{
	$page = 0;
}

$page_size = 6;

if (strtotime($day))
{
	$err_log = new poco_sql_err_log_class();
	$arr_err_log = $err_log->get_err_log_by_day($day, $page, $page_size, $err_code);
	
	$arr_count = $err_log->get_err_log_count_by_day($day);

	//if ($err_code == -1)
	{
		$pages = floor($arr_count[$err_code]["err_count"] / $page_size);
	}

	
	for ($i = 0; $i < $pages; $i++)
	{
		$arr_page_list[] = array(
		'page' 		=> $i,
		'page_show'	=>	$i + 1
		);
	}
	
	$tpl = new SmartTemplate("err_log_list.tpl.htm");
	$tpl->cache_lifetime= 3600*0; //»º´æ3Ğ¡Ê±
	$tpl->assign("day", $day);
	$tpl->assign("err_code", $err_code);
	$tpl->assign("page", $page + 1);
	$tpl->assign("arr_err_log", $arr_err_log);
	$tpl->assign("arr_page_list", $arr_page_list);
	$tpl->output();
}
?>