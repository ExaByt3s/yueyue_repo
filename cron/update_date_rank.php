<?php 
/*
 * 更新用户约拍次数排名
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$date_obj = POCO::singleton('pai_date_rank_class');

$sql = "select count(*) as num,to_date_id from event_db.event_date_tbl where date_status='confirm' and pay_status=1 group by to_date_id order by num desc;";

$date_arr = db_simple_getdata($sql);

foreach($date_arr as $val){
	$user_id = $val['to_date_id'];
	$num = $val['num'];
	
	$sql = "select location_id from pai_db.pai_user_tbl where user_id={$user_id}";
	$user_info = db_simple_getdata($sql,true,101);
	
	$location_id = (int)$user_info['location_id'];
	
	$sql = "select count(*) as num from pai_db.pai_model_audit_tbl where user_id={$user_id} and is_approval=1";
	$is_approval = db_simple_getdata ( $sql, true, 101 );
	if ($is_approval['num'])
	{
		$is_bad_list = 0;
	}
	else
	{
		$is_bad_list = 1;
	}
	
	$sql = "replace into pai_db.pai_date_rank_tbl set user_id={$user_id},num={$num},location_id={$location_id},role='model',is_bad_list={$is_bad_list}";
	db_simple_getdata($sql,false,101);
	
	$cache_key = $date_obj->get_take_photo_times_cache_key($user_id);
	
	POCO::deleteCache ( $cache_key ); //清缓存
}


$sql = "select count(*) as num,from_date_id from event_db.event_date_tbl where date_status='confirm' and pay_status=1 group by from_date_id order by num desc";
$date_arr2 = db_simple_getdata($sql);

foreach($date_arr2 as $val){
	$user_id = $val['from_date_id'];
	$num = $val['num'];
	
	$sql = "select location_id from pai_db.pai_user_tbl where user_id={$user_id}";
	$user_info = db_simple_getdata($sql,true,101);
	
	$location_id = (int)$user_info['location_id'];
	
	$sql = "replace into pai_db.pai_date_rank_tbl set user_id={$user_id},num={$num},location_id={$location_id},role='cameraman'";
	db_simple_getdata($sql,false,101);	
}

$date = date("Y-m-d H:i:s");
echo '约拍次数排名更新成功'.$date;
?>