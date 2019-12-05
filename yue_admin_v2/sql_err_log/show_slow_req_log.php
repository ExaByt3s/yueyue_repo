<?php
/**
 * ²é¿´
 */

include_once 'common.inc.php';
include_once("/disk/data/htdocs232/poco/php_common/sql_err_log/sql_err_log_common.inc.php");

$filename = "/disk/data/htdocs232/mon/slow_req.log";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize ($filename));
fclose($handle);

$tpl = new SmartTemplate("show_slow_req_log.tpl.htm");
$tpl->assign("content", $contents);
$tpl->output();
?>