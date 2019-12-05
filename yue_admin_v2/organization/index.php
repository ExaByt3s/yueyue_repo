<?php
/**
 * @desc:   ป๚นนสืาณ
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/6
 * @Time:   11:30
 * version: 2.0
 */
	include_once 'common.inc.php';
    include_once 'top.php';
    $tpl = new SmartTemplate("index.tpl.htm");

    $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();


 ?>