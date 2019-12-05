<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('pai_topic_common.inc.php');

$tpl = new SmartTemplate("user_list.tpl.html");

$tpl->assign($result);
$tpl->output();
?>
