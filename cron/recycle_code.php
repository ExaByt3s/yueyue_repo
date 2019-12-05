<?php 
/*
 * 回收过期活动码
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$event_details_obj = POCO::singleton ( 'event_details_class' );

$sql = "select * from pai_db.pai_activity_code_tbl where is_end=0 limit 0,100000";
$code_list = db_simple_getdata($sql,false,101);


foreach($code_list as $val)
{
	$event_id = $val['event_id'];
	$code = $val['code'];
	
	$event_info = $event_details_obj->get_event_by_event_id ( $event_id );
	if($event_info['event_status']=='2' || $event_info['event_status']=='3')
	{
		//更新活动已结束状态
		$sql = "update pai_db.pai_activity_code_tbl set is_end=1 where code={$code}";
		db_simple_getdata($sql,false,101);
		
		//回收活动码
		$sql = "update pai_db.pai_new_temp_code_tbl set is_used=0 where code={$code}";
		db_simple_getdata($sql,false,101);
	}
}

?>