<?php
/**
 * 后台菜单
 */

/**
 * 模板
 */

/**
 * common
 */
define("G_YUE_CMS_CHECK_ADMIN",1);
include_once("cms_common.inc.php");

$tpl = new SmartTemplate("index.tpl.htm");
$tpdata = array(
"LEFTFRAME_SRC" => "cms_leftmenu.php",
"MAINFRAME_SRC" => "cms_channel_list.php"
);

$tpl->assign($tpdata);
$tpl->output();
?>