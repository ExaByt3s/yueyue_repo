<?php 


	include_once 'common.inc.php';
	check_authority(array('cameraman'));
    $tpl = new SmartTemplate("cameraman_add_first.tpl.htm");
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();



 ?>