<?php
if (!defined("G_LOCATION_PATH"))
{
	define("G_LOCATION_PATH","./");
}

/**
 * ����smarttemplate
 */
if (!class_exists("SmartTemplate"))
{
	ini_set("include_path",realpath(G_LOCATION_PATH."../include/smarttemplate/"));
	include_once("class.smarttemplate.php");
}

$_CONFIG = array(
"smarttemplate_compiled" => "./_tpl_compiled",
"smarttemplate_cache" =>  "./_cache_dir",
"cache_lifetime" => 600, //��λ����
"reuse_code" => true,
"template_dir" => "./",
"cache_ignore_str_arr" => array("ϵͳ��æ�����Ժ�����","Fatal error"),
"cache_ignore_str" => "ϵͳ��æ�����Ժ�����"
);

?>