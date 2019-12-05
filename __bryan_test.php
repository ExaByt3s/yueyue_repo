<?php
/**
 * 测试
 */

if(!defined('APPROOT')) define('APPROOT','');
define('TASK_MALL_APPROOT',APPROOT."yue_admin/task/");
define('TASK_INCLUDE_ROOT',"include/");
define('TASK_CONFIG_ROOT',"config/");

//define('G_PAI_ECPAY_DEV', 1);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
require_once(TASK_MALL_APPROOT.TASK_INCLUDE_ROOT.'basics.fun.php');

$buyer_user_id = 116127;
$cur_time = time();

$order_sn = trim($_GET['order_sn']);
$order_obj = POCO::singleton('pai_mall_order_class');
$order_activity_obj = POCO::singleton('pai_mall_order_activity_class');

$goods_id = intval($_GET['goods_id']);
$goods_obj = POCO::singleton('pai_mall_goods_class');

$promotion_obj = POCO::singleton('pai_promotion_class');

if( $_GET['step']=='get_promotion_list' )
{
	$cate_id = intval($_POST['cate_id']);
	$list = $promotion_obj->get_promotion_list($cate_id);
	$info = array();
	foreach( $list as $value )
	{
		$info[]=$promotion_obj->get_promotion_full_info($value['promotion_id']);
	}
	var_dump($info);
}
elseif( $_GET['step']=='get_promotion_list_for_show_single' )
{
	$type_target = 'goods';
	$login_user_id = intval($_POST['login_user_id']);
	$goods_id = intval($_POST['goods_id']);
	$stage_id = intval($_POST['stage_id']);
	$prices_type_id = intval($_POST['prices_type_id']);
	$goods_prices = $_POST['goods_prices']*1;
	$show_param_info = array(
		'channel_module' => 'mall_order', //必填
		'channel_gid' => $goods_id, //必填
	);
	$prices_list = array(
		'prices_type_id' => $prices_type_id, //必填
		'stage_id' => $stage_id,
		'goods_prices' => $goods_prices, //必填
	);
	$res = $promotion_obj->get_promotion_list_for_show_single(116127, $type_target, $show_param_info, $prices_list, true);
	var_dump($res);
}
elseif( $_GET['step']=='get_promotion_info_for_show_multiple' )
{
	$type_target = 'goods';
	$login_user_id = $_POST['login_user_id'];
	$show_param_info = array(
	 	'channel_module' => 'mall_order', //必填
	 	'org_user_id' => 0,
	 	'location_id' => 0,
	 	'seller_user_id' => 0,
	 	'mall_type_id' => 0,
		'channel_gid' => 2119604, //必填
	);
	$prices_list = array(
		array(
			'prices_type_id' => 76, //必填
			'goods_prices' => 3, //必填
		),
		array(
			'prices_type_id' => 77, //必填
			'goods_prices' => 5, //必填
		)
	);
	$res = $promotion_obj->get_promotion_info_for_show_multiple($login_user_id, $type_target, $show_param_info, $prices_list, false);
	var_dump($res);
}

elseif( $_GET['step']=='order_use_promotion' )
{
//	$buyer_user_id = 116127;
//	$type_target = 'goods';
//	$goods_id = intval($_POST['goods_id']);
//	$prices_type_id = intval($_POST['prices_type_id']);
//	$goods_prices = $_POST['goods_prices']*1;
//	$quantity = intval($_POST['quantity']);
//	$show_param_info = array(
//		'channel_module' => 'mall_order', //必填
//		'channel_gid' => $goods_id, //必填
//	);
//	$prices_list = array(
//		'prices_type_id' => $prices_type_id, //必填
//		'goods_prices' => $goods_prices, //必填
//	);
//	$res = $promotion_obj->get_promotion_list_for_show_single($buyer_user_id, $type_target, $show_param_info, $prices_list, false);
//
//	$detail_list = array(
//		array(
//		'goods_id' => $goods_id,
//		'prices_type_id' => $prices_type_id,
//		'service_time' => strtotime('2015-10-30 20:30:00'),
//		'service_location_id' => 101007043,
//		'service_address' => '详细地址',
//		'service_people' => 2,
//		'prices' => $goods_prices,
//		'quantity' => $quantity,
//		'goods_promotion_id' => $res[0]['promotion_id'],
//		),
//	);
//	$more_info = array(
//		'seller_user_id' => 120632, //卖家用户ID，特殊服务必填，正常服务忽略
//		'description' => '模拟促销测试', //描述、备注
//		'is_auto_accept' => 0, //是否自动接受，下单、支付、接受不发送通知
//		'is_auto_sign' => 0, //是否自动签到，签到、评价不发送通知
//		'referer' => 'wap', //订单来源，app weixin pc wap oa
//		'order_promotion_id' => 0, //订单促销id
//	);
//	$submit_order = $order_obj->submit_order($buyer_user_id, $detail_list, $more_info=array());
//	var_dump($submit_order);
}

elseif( $_GET['step']=='get_order_info_test' )
{
	$order_sn = $_POST['order_sn'];
	$buyer_user_id = $_POST['buyer_user_id'];
	$res = POCO::singleton('pai_mall_order_test_class')->get_order_full_info($order_sn,$buyer_user_id);
	var_dump($res);
}
elseif( $_GET['step']=='add_user_deal' )
{
//	set_time_limit(0);
//	ini_set("memory_limit","512M");
//	$buyer_user_id = 131432;
//	$seller_user_id = 131394;
//	$res = POCO::singleton('pai_mall_follow_user_class')->add_user_deal($buyer_user_id, $seller_user_id);
//	var_dump($res);
//	exit;
//	$order_list = $order_obj->get_order_list(-1,8,false,'order_id > 51871','order_id asc','0,3000','order_id,buyer_user_id,seller_user_id');
//	foreach( $order_list as$key=>$order_info )
//	{
//		$buyer_user_id = intval($order_info['buyer_user_id']);
//		$seller_user_id = intval($order_info['seller_user_id']);
//		POCO::singleton('pai_mall_follow_user_class')->add_user_deal($buyer_user_id, $seller_user_id);
//		echo $key.' || '.$order_info['order_id']."<br/>";
//	}
//	unset($order_list);
}
elseif( $_GET['step']=='get_order_list_by_goods_ids' )
{
	$goods_ids = array('2117726');
	$res = $order_obj->get_order_list_by_goods_ids(-1,-1,$goods_ids,true);
	var_dump($res);
}

elseif( $_GET['step']=='get_goods_info' )
{
	$goods_id = intval($_GET['goods_id']);
	$res = POCO::singleton('pai_mall_goods_class')->get_goods_info($goods_id);
	print_r($res);
}

elseif( $_GET['step']=='refund_order' )
{
	$order_id = $_GET['order_id'];
	//退还促销数量
//	$promotion_rst = POCO::singleton('pai_promotion_class')->refund_promotion_by_oid('mall_order', $order_id);
}
elseif( $_GET['step']=='decrease_real_quantity_user' )
{
//	$rest = $promotion_obj->increase_real_quantity_user(3, 131432, 1, 'mall_order', 2122333, 1443064317, 2);
//	$rs = $promotion_obj->increase_real_quantity_sku(3, 'mall_order', 2122333, 1443064317, 2);
}
elseif( $_GET['step']=='get_cate_list_all' )
{
	$cate_list = $promotion_obj->get_cate_list_all(0, 0);
	var_dump($cate_list);
}
elseif( $_GET['step']=='get_order_list_for_buyer' )
{
//	$status = array(1,2,8);
	$buyer_user_id = 128279;
	$rst = $order_obj->get_order_list_for_buyer($buyer_user_id,-1,-1,true,'');
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_pay_num' )
{
	$goods_id = intval($_GET['goods_id']);
	$ret = $order_obj->get_order_pay_num($goods_id);
	var_dump($ret);
}
elseif( $_GET['step']=='get_activity' )
{
	$rst = $goods_obj->get_goods_info(2124194);
	var_dump($rst);
}
elseif( $_GET['step']=='submit_order_activity' )
{
	$buyer_user_id = '100293';
	$activity_id = intval($_GET['activity_id']);
	if( $activity_id<0 )
	{
		die('错误');
	}
	$activity_info = $goods_obj->get_goods_info($activity_id);
	$stage_id = $activity_info['prices_data_list'][0]['type_id'];
	$service_time = $activity_info['prices_data_list'][0]['time_s'];
	$prices_type_id = $activity_info['prices_data_list'][0]['prices_list_data'][0]['id'];

	$activity_list = array( array(
	  	'activity_id' => $activity_id, //活动ID
		'stage_id' => $stage_id,
	  	'prices_type_id' => $prices_type_id,
	  	'quantity' => 2, //数量
	   	'activity_promotion_id' => 0, //活动促销ID
		'service_cellphone' => 18681078009,
	),);
	$more_info = array(
	  	'description' => '活动订单测试', //描述、备注
	   	'is_auto_accept' => 0, //是否自动接受，下单、支付、接受不发送通知
	   	'is_auto_sign' => 0, //是否自动签到，签到、评价不发送通知
	   	'referer' => 'wap', //订单来源，app weixin pc wap oa
	);
	$rst = $order_obj->submit_order_activity($buyer_user_id, $activity_list, $more_info);
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_full_info' )
{
	//851700875 活动测试订单号
	$rst = $order_obj->get_order_full_info($_GET['order_sn']);
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_full_list' )
{
	//851700875 活动测试订单号
	$rst = $order_obj->get_order_full_list(-1,-1, false, '', '', '0,20', '*', '' , 116127);
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_list_for_activity' )
{
	$user_id = '116127';
	$status = -1;
	$rst = $order_obj->get_activity_list_by_order_for_seller($user_id, $status, true);
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_list_by_activity_stage' )
{
	$rst = $order_obj->get_order_list_by_activity_stage(2131496, 14482696530,-1);
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_list_for_seller' )
{
	$rst = $order_obj->get_order_list_for_seller(120632, -1, -1);
	var_dump($rst);
}
elseif( $_GET['step']=='get_activity_stage_info_by_order' )
{
	$rst = $order_obj->get_activity_stage_info_by_order(2128751, 144775395202128751, -1);
	var_dump($rst);
}
elseif( $_GET['step']=='change_order_price' )
{
	$rst = $order_obj->change_order_price(771726553, 110371, 2, '活动订单改价测试');
	var_dump($rst);
}
elseif( $_GET['step']=='get_detail_list' )
{
	$rst = $order_obj->get_detail_list_all(172854);
	var_dump($rst);
}
elseif( $_GET['step']=='get_promotion_list_for_use_single' )
{
	$order_param_info = array('channel_module'=>'mall_order','order_total_amount'=>20);
	$prices_info = array(
	 	'channel_gid' => 2125909, //必填
	  	'stage_id'=> 14471471420,
 		'prices_type_id' => 144714739286227, //必填
	 	'goods_prices' => 10, //必填
	 );
	$rst = POCO::singleton('pai_promotion_class')->get_promotion_list_for_use_single(116127, 'goods', $order_param_info, $prices_info, 2, false);
	var_dump($rst);
}
elseif($_GET['step']=='get_order_list_by_activity_id_for_buyer')
{
	$user_id = 100004;
	$activity_id = 2125483;
	$status = 8; //订单成功状态（已签到）
	$b_select_count = true; //返回该状态下订单数目
	$rst = $order_obj->get_order_list_by_activity_id_for_buyer($user_id, $activity_id, $status, $b_select_count);
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_number_for_seller' )
{
	$rst = $order_obj->get_order_number_by_activity_for_seller($buyer_user_id);
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_list_of_paid_by_stage' )
{
	$rst = $order_obj->get_order_list_of_paid_by_stage('2131569', '14483487670', $b_select_count=false, $order_by='', $limit='0,20');
	var_dump($rst);
}
elseif( $_GET['step']=='get_order_number_of_stage_for_seller' )
{
	$rst = $order_obj->get_order_number_of_stage_for_seller(120632, 2125909, 14471473122);
	var_dump($rst);
}
elseif( $_GET['step']=='sum_order_quantity_of_paid_by_activity' )
{
	$rs = $order_obj->sum_order_quantity_of_paid_by_activity(2125909);
	var_dump($rs);
}
elseif( $_GET['step']=='get_order_number_by_stage_for_seller' )
{
	$rst = $order_obj->get_order_number_by_stage_for_seller(104992, 2131507, 14482773930);
	var_dump($rst);
}
elseif( $_GET['step']=='get_goods_prices' )
{
	$goods_prices_rst = $goods_obj->get_goods_prices(2125880, array('num'=>1, 'type_id'=>310));
	var_dump($goods_prices_rst);

}