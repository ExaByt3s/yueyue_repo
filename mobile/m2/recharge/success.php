<?php
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/../../yue_res_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/task_auth_common.inc.php');

$head_html = include_once($file_dir. '/../webcontrol/head.php');

$tpl = new SmartTemplate("success.tpl.html");

$tpl ->assign('head_html',$head_html);
$tpl->output();
?>
