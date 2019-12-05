<?php

/**
 * ��ȡ�����Ż�ȯ
 */

include_once('../common.inc.php');

if(empty($yue_login_id))
{
	$output_arr['list'] = array();
	$output_arr['code'] = 0;

	mobile_output($output_arr,false);

	die();
} 

//��ʱ��־
//pai_log_class::add_log(array(), 'get_user_coupon_list_by_check', 'coupon');

/**
 * ҳ����ղ���
 */
$model_id = intval($_INPUT['model_user_id']);

// ��ȡ����id
$relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
$org_info = $relate_org_obj->get_org_info_by_user_id($model_id);
$org_user_id = (int)$org_info['org_id'];

$module_type = trim($_INPUT['module_type']);
$order_total_amount = trim($_INPUT['order_total_amount']);
if( $module_type=='yuepai' )
{
	$date_id = intval($_INPUT['date_id']);
	$param_info = array(
		'channel_module' => $module_type,
		'channel_oid' => $date_id,
		'module_type' => $module_type, // yuepai waipai
		'order_total_amount' => $order_total_amount, // �����ܶ�
		'model_user_id' => $model_id, // ģ���û�ID ��������0
		'org_user_id' => $org_user_id, // ����ID ��������0
		'mall_type_id' => '31', //����Ʒ�࣬����Լ�ġ��̳�Լ��Ʒ��
		'seller_user_id' => $model_id, //�̼��û�ID�������̳�
	);
}
elseif( $module_type=='waipai' )
{
	$event_id = intval($_INPUT['event_id']);
	$table_id = intval($_INPUT['table_id']);
	$enroll_id = intval($_INPUT['enroll_id']);
	if( $enroll_id<1 )
	{
		$enroll_obj = POCO::singleton('event_enroll_class');
		$enroll_info = $enroll_obj->get_enroll_info_by_event_id_and_user_id_and_table_id($event_id, $yue_login_id, $table_id);
		$enroll_id = intval($enroll_info['enroll_id']);
	}
	
	//��ȡ��֯���û�ID
	$details_obj = POCO::singleton('event_details_class');
	$event_info = $details_obj->get_event_by_event_id($event_id);
	$event_user_id = get_relate_yue_id($event_info['user_id']);
	$location_id = intval($event_info['location_id']);
	
	$param_info = array(
		'channel_module' => $module_type,
		'channel_oid' => $enroll_id,
		'module_type' => $module_type, // yuepai waipai
		'order_total_amount' => $order_total_amount, // �����ܶ�
		'model_user_id' => 0, //ģ���û�ID��Լ�ġ�ר�⣩
		'org_user_id' => $org_user_id, // ����ID
		'location_id' => $location_id, //����ID
		'event_id' => $event_id, //�ID
		'event_user_id' => $event_user_id, //���֯���û�ID
		'seller_user_id' => $event_user_id, //�̼��û�ID�������̳�
	);
}
elseif( $module_type=='task_request' )
{
	$quotes_id = intval($_INPUT['quotes_id']);
	
	//��ȡ������Ϣ
	$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
	$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
	$service_id = intval($quotes_info['service_id']);
	$order_total_amount = trim($_INPUT['order_pay_amount']);
	
	$param_info = array(
		'channel_module' => $module_type,
		'channel_oid' => $quotes_id,
		'module_type' => $module_type, //yuepai waipai
		'order_total_amount' => $order_total_amount, //�����ܶ�
		'service_id' => $service_id, //TT��������
		'seller_user_id' => intval($quotes_info['user_id']), //�̼��û�ID�������̳�
	);
}
elseif( $module_type=='mall_order' )
{
	$order_sn = trim($_INPUT['order_sn']);
	
	//��������
	$mall_order_obj = POCO::singleton('pai_mall_order_class');
	$param_info = $mall_order_obj->get_coupon_param_info_by_order_sn($order_sn, $yue_login_id);
	if( empty($param_info) )
	{
		$output_arr['list'] = array();
		$output_arr['code'] = 0;
		mobile_output($output_arr, false);
		die();
	}
}
else
{
	$param_info = array();
}

/*
 * �����û������Ż�ȯ
 * 
 */
$coupon_obj = POCO::singleton('pai_coupon_class');

$limit_coupon = false; //�Ƿ�����ʹ���Ż�ȯ
$limit_message = '';
$ret = $coupon_obj->get_user_coupon_list_by_check($yue_login_id, 1, $param_info, false, 'face_value DESC,end_time ASC,coupon_id desc', $limit_coupon, $limit_message);
foreach ($ret as $key => $value) 
{
	$ret[$key]['face_value'] = $ret[$key]['face_value'] * 1;
	$ret[$key]['can_select'] = 1;
	
}
$output_arr['list'] = $ret;

if( $limit_coupon==false )
{
	$output_arr['code'] = 1;
	$output_arr['message'] = '';
}
else
{
	$output_arr['code'] = 2;
	$output_arr['message'] = '<p style="color:#ff6a6e">'.$limit_message.'</p>';
}

mobile_output($output_arr,false);

?>