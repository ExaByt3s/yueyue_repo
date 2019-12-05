<?php
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/../../yue_res_common.inc.php');

$head_html = include_once($file_dir. '/../webcontrol/head.php');
$quotes_id = $_GET['quotes_id'];

$tpl = new SmartTemplate("pay.tpl.html");

$tpl ->assign('quotes_id',$quotes_id);
$tpl ->assign('head_html',$head_html);
$tpl->output();
?>