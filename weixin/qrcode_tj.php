<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$id = $_GET['id'];
$id = (int)$id;

$url = urldecode($_GET['url']);
$ip = get_client_ip();
$time = time();
$sql = "insert into test.qrcode_tj_tbl set type_id={$id},add_time={$time},ip='{$ip}'";
db_simple_getdata($sql,false,101);

header("Location: {$url}");

?>