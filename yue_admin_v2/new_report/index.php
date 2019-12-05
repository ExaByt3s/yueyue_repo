<?php
/**
 * @desc:   首页展示导航
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/23
 * @Time:   10:28
 * version: 1.0
 */
include_once('common.inc.php');
include_once('top.php');
$tpl = new SmartTemplate(TEMPLATES_ROOT.'index.tpl.htm');

$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();
