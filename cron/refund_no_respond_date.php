<?php
/*
 * �Զ�ȡ������Լ��ʱ��û���ܺ;ܾ���Լ��
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//�����˿�΢��֪ͨ
define('DONT_SEND_DATE_REFUND_WEIXIN_MSG',1);

$now = date ( "Y-m-d H:i" );

$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
//
$sql = "select from_date_id,to_date_id,date_id,date_time,event_id from event_db.event_date_tbl where date_status='wait' and pay_status=1 and FROM_UNIXTIME(date_time,'%Y-%m-%d %H:%i')='{$now}';";

$date_arr = db_simple_getdata ( $sql );

foreach ( $date_arr as $val ) {
	$date_id = $val ['date_id'];
	$yue_login_id = $val ['from_date_id'];
	$reason = "ϵͳ�Զ��˿�";
	
	$date_cancel_obj->submit_date_refund($date_id, $reason, $remark);
	
	$model_nickname = get_user_nickname_by_user_id($val['to_date_id']);
	
    
    //С����
    $model_content = "��δ�����Լ�������ѹ��ڡ�";
	$url = '/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
	send_message_for_10002 ( $val['to_date_id'], $model_content, $url );

	
	//������
	$phone = $user_obj->get_phone_by_user_id($val ['from_date_id']);
	$group_key = 'G_PAI_DATE_MT_IGNORE';
	$data = array(
		'mt_nickname' => $model_nickname,
	);
	$pai_sms_obj->send_sms($phone, $group_key, $data);
}

$date = date ( "Y-m-d H:i:s" );
echo '�Զ�ȡ��û���ܺ;ܾ���Լ�ĳɹ�' . $date;
?>