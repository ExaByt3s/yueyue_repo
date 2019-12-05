<?php
/**
 * @desc:   Òıµ¼Ò³
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/17
 * @Time:   9:26
 * version: 1.0
 */
include_once('common.inc.php');
include_once('top.php');

$tpl = new SmartTemplate( 'index.tpl.htm' );

$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();
