<?php
/**
 * 市场部地推商品，林俊濠，批量签到
 * @author Henry
 * @copyright 2015-11-14
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if( time()>strtotime('2015-12-31 23:59:59') )
{
	die('此链接已失效！');
}

$goods_id_str = '2122762,2123875,2125891,2128487,2128429,2122759,2124012,2126681,2125914,2125917,2125918,2122830,2115298,2119889,2115299,2125398,2117595,2128559';
$goods_id_arr = explode(',', $goods_id_str);

//商品ID
$goods_id = intval($_INPUT['goods_id']);
$goods_id = trim($goods_id);
if( $goods_id<1 || !in_array($goods_id, $goods_id_arr, true) )
{
	die('商品ID错误！');
}

//获取服务信息
$goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_info = $goods_obj->get_goods_info($goods_id);
if( empty($goods_info) )
{
	die('商品不存在');
}
$seller_user_id = intval($goods_info['goods_data']['user_id']);
if( $seller_user_id<1 )
{
	die('商品信息错误');
}

//手机号
$mobile = intval($_INPUT['mobile']);
if( $mobile<1 )
{
	die('手机号码错误！');
}
$user_obj = POCO::singleton('pai_user_class');
$mobile_user_id = $user_obj->get_user_id_by_phone($mobile);
if( $mobile_user_id<1 )
{
	die('手机号码不存在！');
}
if( $mobile_user_id!=$seller_user_id )
{
	die('手机号码不正确！');
}

$sql = "SELECT o.order_sn,o.seller_user_id FROM `mall_db`.`mall_order_detail_tbl` AS d LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON d.order_id=o.order_id WHERE d.goods_id IN ({$goods_id}) AND o.status=2";
$list = db_simple_getdata($sql, false, 101);
if( empty($list) )
{
	die('没有待签到订单');
}

$mall_order_obj = POCO::singleton('pai_mall_order_class');
foreach($list as $info)
{
	if( $mobile_user_id!=$info['seller_user_id'] )
	{
		die('商家ID不一致！');
	}
	$order_sn = trim($info['order_sn']);
	$sign_rst = $mall_order_obj->sign_order_for_system($order_sn, false);
	echo "{$order_sn} {$sign_rst['message']}<br />\r\n";
}
