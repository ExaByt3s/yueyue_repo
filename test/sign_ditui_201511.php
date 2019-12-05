<?php
/**
 * �г���������Ʒ���ֿ�婣�����ǩ��
 * @author Henry
 * @copyright 2015-11-14
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if( time()>strtotime('2015-12-31 23:59:59') )
{
	die('��������ʧЧ��');
}

$goods_id_str = '2122762,2123875,2125891,2128487,2128429,2122759,2124012,2126681,2125914,2125917,2125918,2122830,2115298,2119889,2115299,2125398,2117595,2128559';
$goods_id_arr = explode(',', $goods_id_str);

//��ƷID
$goods_id = intval($_INPUT['goods_id']);
$goods_id = trim($goods_id);
if( $goods_id<1 || !in_array($goods_id, $goods_id_arr, true) )
{
	die('��ƷID����');
}

//��ȡ������Ϣ
$goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_info = $goods_obj->get_goods_info($goods_id);
if( empty($goods_info) )
{
	die('��Ʒ������');
}
$seller_user_id = intval($goods_info['goods_data']['user_id']);
if( $seller_user_id<1 )
{
	die('��Ʒ��Ϣ����');
}

//�ֻ���
$mobile = intval($_INPUT['mobile']);
if( $mobile<1 )
{
	die('�ֻ��������');
}
$user_obj = POCO::singleton('pai_user_class');
$mobile_user_id = $user_obj->get_user_id_by_phone($mobile);
if( $mobile_user_id<1 )
{
	die('�ֻ����벻���ڣ�');
}
if( $mobile_user_id!=$seller_user_id )
{
	die('�ֻ����벻��ȷ��');
}

$sql = "SELECT o.order_sn,o.seller_user_id FROM `mall_db`.`mall_order_detail_tbl` AS d LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON d.order_id=o.order_id WHERE d.goods_id IN ({$goods_id}) AND o.status=2";
$list = db_simple_getdata($sql, false, 101);
if( empty($list) )
{
	die('û�д�ǩ������');
}

$mall_order_obj = POCO::singleton('pai_mall_order_class');
foreach($list as $info)
{
	if( $mobile_user_id!=$info['seller_user_id'] )
	{
		die('�̼�ID��һ�£�');
	}
	$order_sn = trim($info['order_sn']);
	$sign_rst = $mall_order_obj->sign_order_for_system($order_sn, false);
	echo "{$order_sn} {$sign_rst['message']}<br />\r\n";
}
