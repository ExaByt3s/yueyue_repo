<?php
/*
 * 离活动时间1小时提醒组织者签到
 * 离活动时间1小时提醒参与者签到
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$one_hour_later = date("Y-m-d H:i",time()+3600);

$details_obj = POCO::singleton ( 'event_details_class' );
$code_obj = POCO::singleton ( 'pai_activity_code_class' );
$table_obj = POCO::singleton ( 'event_table_class' );
$enroll_obj = POCO::singleton('event_enroll_class');

$sql = "select * from event_db.event_table_tbl where FROM_UNIXTIME(begin_time,'%Y-%m-%d %H:%i')='{$one_hour_later}' and is_open=1;";

$table_arr = db_simple_getdata ( $sql,false,23 );

//提醒组织者
foreach ( $table_arr as $val )
{
	$event_id = $val ['event_id'];
	$table_id = $val ['id'];
	
	$event_info = $details_obj->get_event_by_event_id ( $event_id );
	
	if($event_info['new_version']==2 && $event_info['event_status']==='0')
	{
	
		$yue_id = get_relate_yue_id($event_info['user_id']);	
		
		$table_arr = $table_obj->get_event_table_num_array($event_id);
		$num = $table_arr[$table_id];
	
		if($yue_id)
		{
			$content = $event_info['title'].'活动第'.$num.'场开始时别忘了和摄友扫一扫二维码或者保存电子密码进行签到，费用才会确认到你的账户';
			send_message_for_10002 ( $yue_id, $content );
		}
		
		$where_str = "table_id = {$table_id} and event_id={$event_id} and pay_status=1";
		
		$enroll_list = $enroll_obj->get_enroll_list($where_str, false, '0,1000');
		
		//提现参与者
		foreach($enroll_list as $code_val)
		{
			$enroll_user_id = get_relate_yue_id($code_val['user_id']);
			if($enroll_user_id)
			{
				$content = $event_info['title'].'活动第'.$num.'场拍摄前别忘了出示二维码给组织者进行签到确认。';
				send_message_for_10002 ( $enroll_user_id, $content );
			}
		}
	}
}


$date = date ( "Y-m-d H:i:s" );
echo '提醒签到成功' . $date;
?>