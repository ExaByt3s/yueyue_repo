<?php
/**
 * �����̳ǹ��ڵĶ������������
 * @author Henry
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$cur_time = time(); //��ǰʱ��

$order_obj = POCO::singleton('pai_mall_order_class');
$push_obj = POCO::singleton('pai_information_push');
$pai_sms_obj = POCO::singleton('pai_sms_class');

$result_str = date('Y-m-d H:i:s', $cur_time) . '|';

//������û��������ʱ
$where_str = "status IN (". pai_mall_order_class::STATUS_WAIT_PAY .",". pai_mall_order_class::STATUS_WAIT_SIGN .") AND expire_time>0 AND expire_time<={$cur_time}";
$order_list = $order_obj->get_order_list(0, -1, false, $where_str, 'expire_time ASC,order_id ASC', '0,99999999', '*', 'activity');
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	$is_auto_sign = intval($order_info['is_auto_sign']);
	$status = intval($order_info['status']);
	
	if( $status===pai_mall_order_class::STATUS_WAIT_PAY ) //�������ʱ
	{
		$ret = $order_obj->close_order_for_system_by_activity($order_sn, ''); //ϵͳ�رն���
		if( $ret['result']==1 )
		{
			//ԼԼС����=>�̼�
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '������ʱδ֧��,�ѹرն���';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');

			//ԼԼС����=>���
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content'] = '������ʱδ֧��,�ѹرն���';
			$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$send_data['link_url'] = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
			$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '������ʱδ֧��,�ѹرն���',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
	}
	elseif( $status===pai_mall_order_class::STATUS_WAIT_SIGN && $is_auto_sign!=1 ) //��ǩ�������Զ�ǩ��������ʱ
	{
		$ret = $order_obj->sign_order_for_system_by_activity($order_sn, true); //ϵͳǩ������
		if( $ret['result']==1 )
		{
			//ԼԼС����=>�̼�
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '����һ�ʶ����Ѿ���ʼ48Сʱ��δǩ�����������Զ���������˻��������';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');

			//ԼԼС����=>���
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content'] = '����һ�ʶ����Ѿ���ʼ48Сʱ��δǩ�����������Զ������̼ҵ��˻���';
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

//����ʼ����1Сʱ�������̼Ҽǵ�ǩ����OA����֪ͨ��
$remind_time_str = date('Y-m-d H:i', strtotime('+1 hour', $cur_time)); //һСʱ��
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
		//ԼԼС����=>�̼�
		$send_data = array();
		$send_data['media_type'] = 'notify';
		$send_data['content']    = "�������ʼ��������ɨһɨ�ͻ��Ķ�ά���¼�����������ǩ�����Ա�֤�յ�����";
		$send_data['link_url']   = 'yueseller://goto?user_id=' . $order_info['seller_user_id'] . '&activity_id=' . $activity_id . '&stage_id=' . $stage_id . '&pid=1250043&type=inner_app';
		$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
		$remind_seller_arr[] = $remind_seller_str;
	}
	
	//ԼԼС����=>���
	$send_data = array();
	$send_data['media_type'] = 'notify';
	$send_data['content'] = "�������ʼ�������˳�ʾ��ά����̼�ǩ��Ŷ";
	$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$send_data['link_url'] = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
	$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);
	
	$result_str .= "2^{$order_sn}|";
}

$result_str .= date('Y-m-d H:i:s');
echo $result_str;
