<?php
/*
 * 当模特收到约拍邀请，一小时内无任何操作，则下发短信通知
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$last_one_hour = date("Y-m-d H:i",time()-3600);

$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );


$sql = "select from_date_id,to_date_id,date_id,date_time,event_id,date_price from event_db.event_date_tbl where date_status='wait' and pay_status=1 and FROM_UNIXTIME(add_time,'%Y-%m-%d %H:%i')='{$last_one_hour}';";

$date_arr = db_simple_getdata ( $sql );

foreach ( $date_arr as $val )
{
	$model_user_id = $val ['to_date_id'];
	$cameraman_nickname = get_user_nickname_by_user_id ( $val ['from_date_id'] );
	$price = $val ['date_price'];
	
	$data = array ('pp_nickname' => $cameraman_nickname,"amount"=>$price);
	$phone = $user_obj->get_phone_by_user_id($model_user_id);
	$pai_sms_obj->send_sms ( $phone, 'G_PAI_DATE_MT_PENDING', $data, $model_user_id );
}

$date = date ( "Y-m-d H:i:s" );
echo '提醒模特还没接受邀请成功' . $date;
?>