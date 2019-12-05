<?php 
/*
 * 定时检查全文服务器有无异常
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$ret = event_fulltext_search($time_querys,$price_querys,$start_querys, false, "0,5");

echo json_encode($ret);

?>