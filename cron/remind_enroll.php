<?php
/*
 * 活动的正选用户未满额的情况下, 在订单生成时间的1小时之后提醒用户支付
 * 
 * 
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



$one_hour_later = date("Y-m-d H:i",time()-3600);

$details_obj = POCO::singleton ( 'event_details_class' );
$table_obj = POCO::singleton ( 'event_table_class' );
$enroll_obj  = POCO::singleton('event_enroll_class');
$pai_config_obj = POCO::singleton ( 'pai_config_class' );

$waipai_arr = $pai_config_obj->big_waipai_event_id_arr();

$sql = "select event_id,table_id,user_id,enroll_num from event_db.event_enroll_tbl where status=3 and FROM_UNIXTIME(enroll_time,'%Y-%m-%d %H:%i')='{$one_hour_later}';";

$enroll_arr = db_simple_getdata ( $sql,false,23 );

var_dump($enroll_arr);

foreach ( $enroll_arr as $val )
{
	$event_id = $val ['event_id'];
	$table_id = $val ['table_id'];
	$enroll_num = $val ['enroll_num'];

	$event_info = $details_obj->get_event_by_event_id ( $event_id );

	if ($event_info['new_version']==2 && $event_info['event_status']==='0')
	{
		$table_arr = $table_obj->get_event_table_num_array($event_id);
		
		$num = $table_obj->get_table_num($event_id, $table_id);
		
		$sum_enroll_num = $enroll_obj->sum_enroll_num($event_id,$table_id,'0');
		
		$total_enroll_sum = $sum_enroll_num+$enroll_num;
		
		$table_num = $table_arr[$table_id];
		
		$yue_id = get_relate_yue_id($val['user_id']);
		if($yue_id && $total_enroll_sum<=$num)
		{
			//大外拍活动
			if(in_array($event_id,$waipai_arr))
			{
				$content = "你参加的".$event_info['title']."活动还有名额，赶快完成付费, 成为正选噢~";
			}
			else
			{
				$content = "你参加的".$event_info['title']."活动第".$table_num."场还有位置，赶快完成付费, 成为正选噢~";
				
			}
			send_message_for_10002 ( $yue_id, $content, $url );	
		}
	}
}

$date = date ( "Y-m-d H:i:s" );
echo '提醒成功' . $date;
?>