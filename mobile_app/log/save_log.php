<?php
/**
 * 接收JS抛过来的错误
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$from_str = trim($_INPUT['from_str']);
$err_level = intval($_INPUT['err_level']);
$url       = trim($_INPUT['url']);
$err_str   = iconv('utf-8', 'gbk', $_REQUEST['err_str']);

$get_str        = serialize(poco_iconv_arr($_GET, 'utf-8', 'gbk'));
$post_str       = serialize(poco_iconv_arr($_POST, 'utf-8', 'gbk'));
$input_str      = serialize(poco_iconv_arr($_INPUT, 'utf-8', 'gbk'));
$ua_str         = trim($_SERVER['HTTP_USER_AGENT']);
$cookie_str     = serialize($_COOKIE);

$add_time = date('Y-m-d H:i:s');

if($err_level)
{
	$row = array(
		'from_str' => $from_str,
		'err_level' => $err_level,
		'url' => $url,
		'get_str' => $get_str,
		'post_str' => $post_str,
		'input_str' => $input_str,
		'cookie_str' => $cookie_str,
		'ua_str' => $ua_str,
		'err_str' => $err_str,
		'add_time' => $add_time,
	);
	$insert_str = db_arr_to_update_str($row);
	
    $sql_str = "INSERT INTO pai_log_db.pai_log_tbl SET {$insert_str}";
    db_simple_getdata($sql_str, TRUE, 101);
}
?>