<?php
/**
 * @desc:
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   11:05
 * version: 1.0
 */

include_once('common.inc.php');
$admin_op_obj  = POCO::singleton('pai_admin_op_class');//²Ù×÷Àà
$tpl = new SmartTemplate( 'admin_op_list.tpl.htm' );

$op_id = intval($op_id);

$str = $admin_op_obj->get_op_sort_list ($op_id);

$tpl->assign('str', $str);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
