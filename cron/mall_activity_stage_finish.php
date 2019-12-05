<?php
/**
 * 处理商城过期的订单
 * @author Henry
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$cur_time = time(); //当前时间

$order_obj = POCO::singleton('pai_mall_order_class');
$push_obj = POCO::singleton('pai_information_push');
$pai_sms_obj = POCO::singleton('pai_sms_class');

$result_str = date('Y-m-d H:i:s', $cur_time) . '|';

$start_time = strtotime(date('Y-m-d H:i:00', $cur_time));//大于等于开始时间
$end_time = strtotime(date('Y-m-d H:i:59', $cur_time));//小于等于结束时间
$stage_list = POCO::singleton('pai_mall_api_class')->get_goods_id_and_type_id_by_time($start_time, $end_time);
//pai_log_class::add_log(array('stage_list'=>$stage_list,'start_time'=>$start_time,'end_time'=>$end_time), 'activity_stage', 'activity_stage');
foreach($stage_list as $stage_info)
{
	$activity_id = intval($stage_info['goods_id']);
	$stage_id = intval($stage_info['type_id']);
	$activity_name = trim($stage_info['goods_name']);
	$stage_name = trim($stage_info['type_id_name']);
	$seller_user_id = intval($stage_info['user_id']);
	
	//获取场次订单列表
	$order_list = $order_obj->get_order_list_by_activity_stage($activity_id, $stage_id, -1, false, 'status IN (2, 8)', '', '0,99999999');
	if( empty($order_list) ) continue;

//	pai_log_class::add_log($order_list, 'order_list', 'activity_stage');

	//约约小助手=>商家
	$send_data = array();
	$send_data['media_type'] = 'text';
	$send_data['content']    = "你组织的{$activity_name}{$stage_name}已结束，可以到商家后台发布活动回顾了。";
	$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
	
	foreach($order_list as $order_info)
	{
		$order_sn = trim($order_info['order_sn']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$activity_name = trim($order_info['activity_name']);
		$stage_name = trim($order_info['stage_name']);
		
		//约约小助手=>买家
		$send_data = array();
		$send_data['media_type'] = 'text';
		$send_data['content'] = "感谢你参与了{$stage_name}活动，将拍摄作品分享到约约的官方合作网站http://photo.poco.cn，将获得更多好评及广泛关注哦。";
		$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);
		
		$result_str .= "1^{$order_sn}|";
	}

}

$result_str .= date('Y-m-d H:i:s');
echo $result_str;