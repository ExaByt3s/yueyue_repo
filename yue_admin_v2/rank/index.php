<?php
/**
 * @desc:  ÁÙÊ±µ÷Õû
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/21
 * @Time:   17:24
 * version: 1.0
 */

include_once 'common.inc.php';
include_once 'top.php';
$tpl = new SmartTemplate("index.tpl.htm");
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
