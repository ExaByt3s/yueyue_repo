<?php 
	include_once 'common.inc.php';
    include_once 'top.php';
    
    $tpl = new SmartTemplate("version_add.tpl.htm"); 

    $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();
 ?>