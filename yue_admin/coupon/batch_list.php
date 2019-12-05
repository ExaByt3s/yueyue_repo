<?php

/**
 * 优惠券总表
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-27 13:36:57
 * @version 1
 */
include_once("common.inc.php");
//优惠券类
$coupon_obj = POCO::singleton('pai_coupon_class');
//分页类
$page_obj   = new show_page();
$show_count = 30;
$tpl        = new SmartTemplate('batch_list.tpl.htm');

$act        = $_INPUT['act'];
$batch_name = $_INPUT['batch_name'] ? trim($_INPUT['batch_name']) : '';
//分类ID
$cate_id   = 0;
$where_str = '';
$setParam  = array();

if ($batch_name)
{
	if(strlen($where_str) > 0) $where_str .= ' AND ';
	$where_str .= "batch_name LIKE '%".mysql_escape_string($batch_name)."%'";
	$setParam['batch_name'] = $batch_name;
}
//echo $where_str;
$total_count = $coupon_obj->get_batch_list($cate_id, true, $where_str);

$page_obj->setvar($setParam);
$page_obj->set ($show_count, $total_count);

$list       = $coupon_obj->get_batch_list($cate_id, false , $where_str,'cate_id DESC,batch_id DESC', $page_obj->limit());

if(!is_array($list)) $list = array();

foreach ($list as $key => $vo)
{
		$list[$key]['add_time'] = date('Y-m-d', $vo['add_time']);
		//剩余的优惠券
		$list[$key]['remain_quantity'] = ($vo['plan_quantity'] - $vo['real_quantity'])*1;

		//已补贴金额
		$list[$key]['cash_amount'] = $coupon_obj->sum_ref_order_cash_amount_by_batch_id($vo['batch_id']);

}

$tpl->assign('list', $list);
$tpl->assign('batch_name', $batch_name);

//共用部分
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();