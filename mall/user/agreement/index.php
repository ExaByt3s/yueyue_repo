<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'agreement/index.tpl.html');


// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

$help_text_config = include_once('/disk/data/htdocs232/poco/pai/mobile/config/help_text.conf.php');

$ret = $help_text_config['text']['agreement'];

$tpl->assign('ret',$ret);
$tpl->output();
?>