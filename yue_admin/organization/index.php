<?php 

	include_once 'common.inc.php';
    include_once 'top.php';
    $tpl = new SmartTemplate("index.tpl.htm");
    //$tpl->assign('audit_list', check_authority_by_list($ret_type = 'display',$authority_list, 'organization', 'is_select'));
    $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();


 ?>