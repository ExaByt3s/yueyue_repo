<?php 
/*
 * 新增用户
 *
*/

	include_once 'common.inc.php';
    $tpl = new SmartTemplate("model_add_first.tpl.htm");
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();



 ?>