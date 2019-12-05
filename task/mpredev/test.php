<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$tpl = $my_app_pai->getView('test.tpl.htm');

$tpl->assign('time', time());  //Ëæ»úÊý


$tpl->output();
 ?>