<?php
/**
 * 市场部地推商品，透仔，批量接受
 * @author Henry
 * @copyright 2015-10-30
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if( time()>strtotime('2015-12-31 23:59:59') )
{
	die('此链接已失效！');
}

$goods_id_str = '2124647,2122759,2124012,2124060,2124183,2124416,2124924,2124945,2124936,2124689,2123840,2124335,2124990,2123469,2125024,2125028,2125029,2124042,2124519,2125037,2123694,2123693,2123690,2123676,2124745,2125002,2124027,2124646,2123874,2125091,2123873';
$sql = "SELECT o.order_sn FROM `mall_db`.`mall_order_detail_tbl` AS d LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON d.order_id=o.order_id WHERE d.goods_id IN ({$goods_id_str}) AND o.status=1";
$list = db_simple_getdata($sql, false, 101);
if( empty($list) )
{
	die('没有待确认订单');
}

$mall_order_obj = POCO::singleton('pai_mall_order_class');
foreach($list as $info)
{
	$order_sn = trim($info['order_sn']);
	$accept_rst = $mall_order_obj->accept_order_for_system($order_sn);
	echo "{$order_sn} {$accept_rst['message']}<br />\r\n";
}
