<?php
/*
 * 提醒签到
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$one_hour_later = date("Y-m-d H:i",time()+3600);

$details_obj = POCO::singleton ( 'event_details_class' );
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');

$sql = "select from_date_id,to_date_id,date_id,date_time,event_id from event_db.event_date_tbl where date_status='confirm' and pay_status=1 and FROM_UNIXTIME(date_time,'%Y-%m-%d %H:%i')='{$one_hour_later}';";

$date_arr = db_simple_getdata ( $sql );

foreach ( $date_arr as $val )
{
	$date_id = $val ['date_id'];
	$event_id = $val ['event_id'];
	$date_time = $val ['date_time'];
	$model_user_id = $val ['to_date_id'];
	$cameraman_user_id = $val ['from_date_id'];
	$model_nickname = get_user_nickname_by_user_id($val['to_date_id']);
	
	$event_status = $details_obj->get_event_info_status($event_id);

	if ($event_status==0)
	{
		$model_content = "拍摄时别忘了和摄影师扫一扫二维码进行签到，费用才会确认到你的账户。";
		
		$cameraman_content = "拍摄时别忘了出示二维码给模特进行签到确认。";
		
		send_message_for_10002 ( $model_user_id, $model_content, $url );
		send_message_for_10002 ( $cameraman_user_id, $cameraman_content, $url );
		
		
		//微信通知
	    $user_id = $val ['from_date_id'];
	    $template_code = 'G_PAI_WEIXIN_CODE_PREV';
	    $data = array('order_no'=>$date_id, 'datetime'=>date("Y年n月j日 H:i",$date_time),'nickname'=>$model_nickname);
	    $version_control = include('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
	    $cache_ver = trim($version_control['wx']['cache_ver']);
	    $to_url = "http://yp.yueus.com/m/wx?{$cache_ver}#mine/consider_details_camera/{$date_id}";
	    $ret = $weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $data, $to_url);
	}
}

$date = date ( "Y-m-d H:i:s" );
echo '提醒签到成功' . $date;
?>