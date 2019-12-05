<?php
/*
 * 活动结束12小时后提现组织者确认活动
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$hour_12_later = date("Y-m-d H:i",time()-3600*12);

$details_obj = POCO::singleton ( 'event_details_class' );
$code_obj = POCO::singleton ( 'pai_activity_code_class' );

$sql = "select * from event_db.event_details_tbl where new_version=2 and event_status='0' and FROM_UNIXTIME(end_time,'%Y-%m-%d %H:%i')='{$hour_12_later}';";

$event_arr = db_simple_getdata ( $sql,false,23 );

//提醒组织者
foreach ( $event_arr as $val )
{
	$user_id = $val ['user_id'];
	$event_id = $val ['event_id'];
	$title = $val ['title'];
	$yue_id = get_relate_yue_id($user_id);
	
	if($yue_id)
	{
		$content = $title.'活动时间结束后24小时，若不做任何操作，系统自动确认活动结束';
		send_message_for_10002 ( $yue_id, $content );
	}
}


$date = date ( "Y-m-d H:i:s" );
echo '提醒成功' . $date;
?>