<?php

include_once('/disk/data/htdocs232/poco/pai/topic/meeting/config/phone_meeting_config.php');//���÷���Ӧ���ε�ID����Ǯ
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$tpl = $my_app_pai->getView('wx_cross_pay.tpl.htm');

$tpl->output();
?>