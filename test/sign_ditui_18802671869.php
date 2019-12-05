<?php
/**
 * 市场部地推商品，深圳酒吧活动，批量签到
 * @author Henry
 * @copyright 2015-10-29
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if( time()>strtotime('2015-11-30 23:59:59') )
{
	die('此链接已失效！');
}

$goods_id_str = '2124647';
$sql = "SELECT o.order_sn FROM `mall_db`.`mall_order_detail_tbl` AS d LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON d.order_id=o.order_id WHERE d.goods_id IN ({$goods_id_str}) AND o.status=2";
$list = db_simple_getdata($sql, false, 101);
if( empty($list) )
{
	die('没有待签到订单');
}

$mall_order_obj = POCO::singleton('pai_mall_order_class');
foreach($list as $info)
{
	$order_sn = trim($info['order_sn']);
	$sign_rst = $mall_order_obj->sign_order_for_system($order_sn, false);
	echo "{$order_sn} {$sign_rst['message']}<br />\r\n";
}
