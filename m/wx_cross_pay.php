<?php

include_once('/disk/data/htdocs232/poco/pai/topic/meeting/config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$tpl = $my_app_pai->getView('wx_cross_pay.tpl.htm');

$tpl->output();
?>