<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); 
$tpl = $my_app_pai->getView('seller_msg_topic.tpl.htm');

$down_load_url = 'http://s.yueus.com/';

$tpl->assign('down_load_url',$down_load_url);
$tpl->output();
?>