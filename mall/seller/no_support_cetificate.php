<?php
include_once 'common.inc.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'cetificate/no_support_cetificate.tpl.html');
$tpl->output();
?>