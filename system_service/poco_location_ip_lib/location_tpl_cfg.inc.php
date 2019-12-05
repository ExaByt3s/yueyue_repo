<?php
if (!defined("G_LOCATION_PATH"))
{
	define("G_LOCATION_PATH","./");
}

/**
 * 设置smarttemplate
 */
if (!class_exists("SmartTemplate"))
{
	ini_set("include_path",realpath(G_LOCATION_PATH."../include/smarttemplate/"));
	include_once("class.smarttemplate.php");
}

$_CONFIG = array(
"smarttemplate_compiled" => "./_tpl_compiled",
"smarttemplate_cache" =>  "./_cache_dir",
"cache_lifetime" => 600, //单位：秒
"reuse_code" => true,
"template_dir" => "./",
"cache_ignore_str_arr" => array("系统繁忙，请稍后再试","Fatal error"),
"cache_ignore_str" => "系统繁忙，请稍后再试"
);

?>