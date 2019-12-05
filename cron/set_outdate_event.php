<?php 
/*
 * 自动处理过期的活动，有签到的自动完成，没签到的自动取消
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$code_obj = POCO::singleton ( 'pai_activity_code_class' );

$next_24_hours = date("Y-m-d H:i",time()-24*3600);



$event_list = get_event_list("new_version=2 and event_status='0' and FROM_UNIXTIME(end_time,'%Y-%m-%d %H:%i')<='{$next_24_hours}'", false, '0,1000', 'last_update_time DESC','0,1');

//print_r($event_list);

foreach($event_list as $val)
{
	$event_id = $val['event_id'];
	$check_event_scan = $code_obj->check_event_code_scan($event_id);
	$yue_login_id = $val['user_id'];
    

	if($check_event_scan)
	{
		$ret = set_event_end_v2($event_id);
	}
	else
	{
		$ret = set_event_status_by_cancel($event_id);
	}
	
	var_dump($ret);
}

$date = date("Y-m-d H:i:s");
echo '自动处理成功'.$date;

?>