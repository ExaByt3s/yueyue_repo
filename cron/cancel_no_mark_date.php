<?php
/*
 * ȡ��48Сʱûǩ����Լ��
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//48Сʱǰ
$last_48_hours = date("Y-m-d H:i",time()-3600*48);

$details_obj = POCO::singleton ( 'event_details_class' );
$code_obj = POCO::singleton ( 'pai_activity_code_class' );
$date_obj = POCO::singleton ( 'event_date_class' );
$payment_obj = POCO::singleton ( 'pai_payment_class' );
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');

$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );

//�ҳ�����״̬Ϊ��ȷ�����Ѹ��Լ��ʱ���ѳ�����48Сʱ�ļ�¼
$sql = "select from_date_id,to_date_id,date_id,date_time,event_id,enroll_id from event_db.event_date_tbl where date_status='confirm' and pay_status=1 and FROM_UNIXTIME(date_time,'%Y-%m-%d %H:%i')='{$last_48_hours}';";

$date_arr = db_simple_getdata ( $sql );

foreach ( $date_arr as $val )
{
	$date_id = $val ['date_id'];
	$event_id = $val ['event_id'];
	$enroll_id = $val ['enroll_id'];
	$date_time = $val ['date_time'];
	$model_user_id = $val ['to_date_id'];
	$cameraman_user_id = $val ['from_date_id'];
	$model_nickname = get_user_nickname_by_user_id ( $model_user_id );
	$cameraman_nickname = get_user_nickname_by_user_id ( $cameraman_user_id );
	
	$event_status = $details_obj->get_event_info_status($event_id);

	//����Ƿ������
	if ($event_status==0)
	{
		$check_scan = $code_obj->check_is_all_scan($enroll_id);
		//�����ûǩ��
		if(!$check_scan)
		{
			//ȫ���˿�
			$pay_info = $payment_obj->cancel_event ( $event_id );
			
			if ($pay_info)
			{
				$date_obj->update_event_date_status ( $date_id, 'cancel_date' );
				$details_obj->set_event_status_by_cancel ( $event_id );
				
				//С����
				$model_content = "��Ӱʦ{$cameraman_nickname}��Լ�ģ��ѳ���48Сʱδǩ����ϵͳ��ȡ��Լ�ġ�";		
				$url = "/mobile/app?from_app=1#mine/consider_details_camera/".$date_id;			
				send_message_for_10002 ( $model_user_id, $model_content, $url );

								
				//������
				$phone = $user_obj->get_phone_by_user_id($cameraman_user_id);
				$group_key = 'G_PAI_DATE_CODE_NO_CHECKED';
				$data = array(
					'mt_nickname' => $model_nickname,
				);
				$pai_sms_obj->send_sms($phone, $group_key, $data);
				
			}
		}
	}
}

$date = date ( "Y-m-d H:i:s" );
echo 'ȡ��48Сʱûǩ����Լ�ĳɹ�' . $date;
?>