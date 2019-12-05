<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./common_head.php');
$tpl = $my_app_pai->getView('success.tpl.htm');

$tpl->assign('time', time());  //Ëæ»úÊý


$tpl->output();
 ?>