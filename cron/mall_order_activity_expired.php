<?php
/**
 * 处理商城过期的订单（活动订单）
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

//处理订单没操作，超时
$where_str = "status IN (". pai_mall_order_class::STATUS_WAIT_PAY .",". pai_mall_order_class::STATUS_WAIT_SIGN .") AND expire_time>0 AND expire_time<={$cur_time}";
$order_list = $order_obj->get_order_list(0, -1, false, $where_str, 'expire_time ASC,order_id ASC', '0,99999999', '*', 'activity');
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	$is_auto_sign = intval($order_info['is_auto_sign']);
	$status = intval($order_info['status']);
	
	if( $status===pai_mall_order_class::STATUS_WAIT_PAY ) //待付款，超时
	{
		$ret = $order_obj->close_order_for_system_by_activity($order_sn, ''); //系统关闭订单
		if( $ret['result']==1 )
		{
			//约约小助手=>商家
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '订单超时未支付,已关闭订单';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');

			//约约小助手=>买家
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content'] = '订单超时未支付,已关闭订单';
			$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$send_data['link_url'] = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
			$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);

			//微信公众号模板消息
			$template_data = array(
				'first' => '订单超时未支付,已关闭订单',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
	}
	elseif( $status===pai_mall_order_class::STATUS_WAIT_SIGN && $is_auto_sign!=1 ) //待签到（非自动签到），超时
	{
		$ret = $order_obj->sign_order_for_system_by_activity($order_sn, true); //系统签到订单
		if( $ret['result']==1 )
		{
			//约约小助手=>商家
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '你有一笔订单已经开始48小时且未签到，款项已自动到账你的账户，请查收';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');

			//约约小助手=>买家
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content'] = '你有一笔订单已经开始48小时且未签到，款项已自动到账商家的账户。';
			$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$send_data['link_url'] = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
			$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);
		}
	}
	else
	{
		$ret = array(
			'result' => 0,
			'message' => 'else',
		);
	}
	$result_str .= "1^{$order_sn}^{$status}^{$ret['result']}^{$ret['message']}|";
}

//离活动开始还有1小时，提醒商家记得签到（OA单不通知）
$remind_time_str = date('Y-m-d H:i', strtotime('+1 hour', $cur_time)); //一小时后
$sql = "SELECT a.order_activity_id,a.activity_id,a.stage_id,a.order_id,a.service_start_time,o.order_sn,o.buyer_user_id,o.seller_user_id,o.status FROM `mall_db`.`mall_order_activity_tbl` AS a LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON a.order_id=o.order_id";
$sql .= " WHERE o.order_type='activity' AND o.status IN (" . pai_mall_order_class::STATUS_WAIT_SIGN . ") AND a.service_start_time>0 AND FROM_UNIXTIME(a.service_start_time,'%Y-%m-%d %H:%i')='{$remind_time_str}' AND o.referer<>'oa'";
$order_list = db_simple_getdata($sql, false, 101);
$remind_seller_arr = array();
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	$activity_id = intval($order_info['activity_id']);
	$stage_id = intval($order_info['stage_id']);
	$remind_seller_str = $seller_user_id. '_' . $activity_id . '_' . $stage_id;
	if( !in_array($remind_seller_str, $remind_seller_arr, true) )
	{
		//约约小助手=>商家
		$send_data = array();
		$send_data['media_type'] = 'notify';
		$send_data['content']    = "活动即将开始，别忘了扫一扫客户的二维码或录入数字码进行签到，以保证收到款项";
		$send_data['link_url']   = 'yueseller://goto?user_id=' . $order_info['seller_user_id'] . '&activity_id=' . $activity_id . '&stage_id=' . $stage_id . '&pid=1250043&type=inner_app';
		$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
		$remind_seller_arr[] = $remind_seller_str;
	}
	
	//约约小助手=>买家
	$send_data = array();
	$send_data['media_type'] = 'notify';
	$send_data['content'] = "活动即将开始，别忘了出示二维码给商家签到哦";
	$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$send_data['link_url'] = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
	$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);
	
	$result_str .= "2^{$order_sn}|";
}

$result_str .= date('Y-m-d H:i:s');
echo $result_str;
