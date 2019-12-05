<?
include_once("cdn_refresh_helper_class.inc.php");

$cdn_refresh_helper_obj = new cdn_refresh_helper_class();
$_REQUEST[_debug]=1;
var_dump($cdn_refresh_helper_obj->refresh_dirs("http://www.poco.cn/test/*"));


?>