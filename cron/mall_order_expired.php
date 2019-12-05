<?php
/**
 * �����̳ǹ��ڵĶ���
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
$where_str = "status IN (". pai_mall_order_class::STATUS_WAIT_PAY .",". pai_mall_order_class::STATUS_WAIT_CONFIRM .",". pai_mall_order_class::STATUS_WAIT_SIGN .") AND expire_time>0 AND expire_time<={$cur_time}";
$order_list = $order_obj->get_order_list(0, -1, false, $where_str, 'expire_time ASC,order_id ASC', '0,99999999', '*', 'detail');
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	$is_auto_accept = intval($order_info['is_auto_accept']);
	$is_auto_sign = intval($order_info['is_auto_sign']);
	$status = intval($order_info['status']);
	
	if( $status===pai_mall_order_class::STATUS_WAIT_PAY ) //�������ʱ
	{
		$ret = $order_obj->close_order_for_system($order_sn); //ϵͳ�رն���
	}
	elseif( $status===pai_mall_order_class::STATUS_WAIT_CONFIRM && $is_auto_accept!=1 ) //��ȷ�ϣ����Զ����ܣ�����ʱ
	{
		$ret = $order_obj->close_order_for_system($order_sn); //ϵͳ�رն���
		if( $ret['result']==1 )
		{
			//ԼԼС����=>�̼�
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '����һ��δ����Ķ����ѹ���';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
		}
	}
	elseif( $status===pai_mall_order_class::STATUS_WAIT_SIGN && $is_auto_sign!=1 ) //��ǩ�������Զ�ǩ��������ʱ
	{
		$ret = $order_obj->sign_order_for_system($order_sn, true); //ϵͳǩ������
		if( $ret['result']==1 )
		{
			//ԼԼС����=>�̼�
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '����һ�ʶ����Ѿ���ʼ48Сʱ��δǩ�����������Զ���������˻��������';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
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

//�����֧�����̼�1Сʱ��û�в�����OA����֪ͨ��
$remind_time_str = date('Y-m-d H:i', strtotime('-1 hour', $cur_time)); //һСʱǰ
$where_str = "status IN (". pai_mall_order_class::STATUS_WAIT_CONFIRM .") AND is_pay=1 AND FROM_UNIXTIME(pay_time,'%Y-%m-%d %H:%i')='{$remind_time_str}' AND referer<>'oa'";
$order_list = $order_obj->get_order_list(0, -1, false, $where_str, 'pay_time ASC,order_id ASC', '0,99999999', '*', 'detail');
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	
	//��ȡ����ǳ�
	$buyer_nickname = get_user_nickname_by_user_id($buyer_user_id);
	
	//��ȡ�̼��ֻ���
	$pai_user_obj = POCO::singleton('pai_user_class');
	$seller_cellphone = $pai_user_obj->get_phone_by_user_id($seller_user_id);
	
	//���Ͷ���
	$sms_data = array(
		'buyer_nickname' => $buyer_nickname,
		'amount' => $order_info['total_amount'],
	);
	$pai_sms_obj->send_sms($seller_cellphone, 'G_PAI_MALL_ORDER_WAIT_CONFIRM_REMIND_SELLER', $sms_data);
	
	$result_str .= "2^{$order_sn}|";
}

//�����ʼ����12Сʱ�����������߼ǵ�ǩ����OA����֪ͨ��
$remind_time_str = date('Y-m-d H:i', strtotime('+12 hour', $cur_time)); //ʮ��Сʱ��
$sql = "SELECT d.order_detail_id,d.order_id,d.service_time,o.order_sn,o.buyer_user_id,o.seller_user_id,o.status FROM `mall_db`.`mall_order_detail_tbl` AS d LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON d.order_id=o.order_id";
$sql .= " WHERE o.status IN (" . pai_mall_order_class::STATUS_WAIT_SIGN . ") AND d.service_time>0 AND FROM_UNIXTIME(d.service_time,'%Y-%m-%d %H:%i')='{$remind_time_str}' AND o.referer<>'oa'";
$order_list = db_simple_getdata($sql, false, 101);
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	
	//ԼԼС����=>���
	$send_data = array();
	$send_data['media_type'] = 'notify';
	$send_data['content']    = '������12Сʱ��ʼ�������˳�ʾ��ά����̼�ǩ��Ŷ�������˿�����̼���ϵ';
	$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$send_data['link_url']   = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
	$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);
	
	$result_str .= "3^{$order_sn}|";
}

//�����ʼ����1Сʱ�������̼Ҽǵ�ǩ����OA����֪ͨ��
$remind_time_str = date('Y-m-d H:i', strtotime('+1 hour', $cur_time)); //һСʱ��
$sql = "SELECT d.order_detail_id,d.order_id,d.service_time,o.order_sn,o.buyer_user_id,o.seller_user_id,o.status FROM `mall_db`.`mall_order_detail_tbl` AS d LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON d.order_id=o.order_id";
$sql .= " WHERE o.status IN (" . pai_mall_order_class::STATUS_WAIT_SIGN . ") AND d.service_time>0 AND FROM_UNIXTIME(d.service_time,'%Y-%m-%d %H:%i')='{$remind_time_str}' AND o.referer<>'oa'";
$order_list = db_simple_getdata($sql, false, 101);
foreach($order_list as $order_info)
{
	$order_sn = trim($order_info['order_sn']);
	$buyer_user_id = intval($order_info['buyer_user_id']);
	$seller_user_id = intval($order_info['seller_user_id']);
	
	//ԼԼС����=>�̼�
	$send_data = array();
	$send_data['media_type'] = 'notify';
	$send_data['content']    = '���񼴽���ʼ��������ɨһɨ�ͻ��Ķ�ά���¼�����������ǩ�����Ա�֤�յ�����';
	$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
	$push_obj->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
	
	//ԼԼС����=>���
	$send_data = array();
	$send_data['media_type'] = 'notify';
	$send_data['content'] = '���񼴽���ʼ�������˳�ʾ��ά����̼�ǩ��Ŷ';
	$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
	$send_data['link_url'] = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
	$push_obj->message_sending_for_system($buyer_user_id, $send_data, 10002);
	
	$result_str .= "4^{$order_sn}|";
}

$result_str .= date('Y-m-d H:i:s');
echo $result_str;
