<?php
/**
 * @desc:   查询已经结算历史
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/9
 * @Time:   16:54
 * version: 2.0
 */
include('common.inc.php');
$payment_obj = POCO::singleton('pai_payment_class');
$page_obj = new show_page ();
$show_count = 5;
$tpl  = new SmartTemplate("close_billing_list.tpl.htm");

$user_id = intval($_INPUT['user_id']);
if ($user_id <1)
{
    echo "<script type='text/javascript'>window.alert('非法操作');parent.location.href='org_list_v2.php';</script>";
    exit;
}
$where_str = '';
$setParam  = array();

$setParam['user_id'] = $user_id;

$total_count = $payment_obj->get_settle_list($user_id, true, $where_str);
$page_obj->setvar($setParam);
$page_obj->set ( $show_count, $total_count );
$list = $payment_obj->get_settle_list($user_id,false, $where_str,'settle_date DESC,settle_id DESC', $page_obj->limit(), $fields = '*');

$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign('total_count', $total_count);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();
