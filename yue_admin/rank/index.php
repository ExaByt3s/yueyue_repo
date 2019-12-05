<?php 

	include_once 'common.inc.php';
    include_once 'top.php';

    $location_id_root = trim($authority_list[0]['location_id']);
    $tpl = new SmartTemplate("index.tpl.htm");
    $tpl->assign('location_id_root',$location_id_root);
    $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>