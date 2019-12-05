<?php
/**
 * 设置smarttemplate
 */
if (!class_exists("SmartTemplate", false))
{
	ini_set("include_path", realpath(G_POCO_APP_PATH . "/include/smarttemplate/"));
	include_once("class.smarttemplate.php");
}

global $_CONFIG;
$_CONFIG = array(
	"smarttemplate_compiled" => "./_tpl_compiled",
	"smarttemplate_cache" => "./_cache_dir",
	"cache_lifetime" => 600, //单位：秒
	"reuse_code" => true,
	"template_dir" => G_POCO_APP_TEMPLATE_DIR,
	"cache_ignore_str_arr" => array("系统繁忙，请稍后再试","Fatal error"),
	"cache_ignore_str" => "系统繁忙，请稍后再试",
);
