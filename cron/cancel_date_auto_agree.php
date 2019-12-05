<?php
/*
 * 取消约拍自动同意
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$cancel_log_obj = POCO::singleton ( 'event_date_cancel_log_class' );
$payment_obj = POCO::singleton ( 'pai_payment_class' );
$date_obj = POCO::singleton ( 'event_date_class' );
$event_details_obj = POCO::singleton ( 'event_details_class' );
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');

//找出log表里只有一条记录的
$sql = "select count(*) as c,date_id,add_time from event_db.event_date_cancel_log_tbl group by date_id having c=1;";

$date_arr = db_simple_getdata ( $sql );

foreach ( $date_arr as $val )
{
	$date_id = $val ['date_id'];
	$add_time = $val ['add_time'];
	$now = time ();
	
	$date_info = $date_obj->get_date_info ( $date_id );
	
	//大于12小时就自动同意取消
	if ($now - $add_time > 43200 && $date_info['date_status']=='confirm')
	{
		$event_id = $date_info ['event_id'];
		$model_user_id = $date_info ['to_date_id'];
		$model_nickname = get_user_nickname_by_user_id($val['to_date_id']);
		//全额退款
		$pay_info = $payment_obj->cancel_event ( $event_id );
		if ($pay_info['error']===0)
		{
			$date_obj->update_event_date_status ( $date_id, 'cancel_date' );
			
			$event_details_obj->set_event_status_by_cancel ( $event_id );
			
			$cancel_log_obj->add_date_cancel_log ( $date_id, $model_user_id, 'auto_agree' );
			
			//微信通知
		    $user_id = $val ['from_date_id'];
		    $template_code = 'G_PAI_WEIXIN_DATE_IGNORE';
		    $data = array('order_no'=>$date_id, 'datetime'=>date("Y年n月j日 H:i",$val ['date_time']),'nickname'=>$model_nickname);
		    $version_control = include('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
		    $cache_ver = trim($version_control['wx']['cache_ver']);
		    $to_url = "http://app.yueus.com/";
		    $ret = $weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $data, $to_url);
		}
	
	}
}

$date = date ( "Y-m-d H:i:s" );
echo '取消约拍自动同意成功' . $date;
?>