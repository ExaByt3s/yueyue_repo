<?php
/**
 * 市场部地推商品，林俊濠，批量接受
 * @author Henry
 * @copyright 2015-11-14
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if( time()>strtotime('2015-12-31 23:59:59') )
{
	die('此链接已失效！');
}

$goods_id_str = '2122762,2123875,2125891,2128487,2128429,2122759,2124012,2126681,2125914,2125917,2125918,2122830,2115298,2119889,2115299,2125398,2117595,2128559';
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
