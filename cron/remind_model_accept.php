<?php
/*
 * 在摄影师下单6小时后,模特没操作 ，提醒模特还没接受邀请
 */

exit;
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$last_six_hour = date("Y-m-d H:i",time()-3600*6);


$sql = "select from_date_id,to_date_id,date_id,date_time,event_id from event_db.event_date_tbl where date_status='wait' and pay_status=1 and FROM_UNIXTIME(add_time,'%Y-%m-%d %H:%i')='{$last_six_hour}';";

$date_arr = db_simple_getdata ( $sql );

foreach ( $date_arr as $val )
{
	$date_id = $val ['date_id'];
	$date_time = $val ['date_time'];
	$model_user_id = $val ['to_date_id'];

	$model_content = "你还有未接受的约拍邀请，请尽快处理。";
	$url = '/mobile/app?from_app=1#mine/consider_details_model/' . $date_id;
	send_message_for_10002 ( $model_user_id, $model_content, $url );

}

$date = date ( "Y-m-d H:i:s" );
echo '提醒模特还没接受邀请成功' . $date;
?>