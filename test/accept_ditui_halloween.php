<?php
/**
 * 市场部地推商品，万圣节游园会商品，批量接受
 * @author Henry
 * @copyright 2015-10-25
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if( time()>strtotime('2015-11-30 23:59:59') )
{
	die('此链接已失效！');
}

$goods_id_str = '2124416,2124042,2124183,2124001,2123675,2122705';
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
