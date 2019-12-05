<?php 
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "select * from test.task_seller_tbl where status=0";
$seller_arr = db_simple_getdata($sql,false,101);

foreach($seller_arr as $val)
{
	$sql = "insert ignore pai_task_db.task_seller_tbl set service_id=$val[service_id] ,user_id=$val[user_id]";
	db_simple_getdata($sql,false,101);
	
	$data['user_id'] = $val['user_id'];
	$data['service_id'] = $val['service_id'];
	$data['title'] = $val['name'];
	$data['cellphone'] = $val['cellphone'];
	$data['email'] = $val['email'];
	$data['location_id'] = $val['location_id'];
	$data['address'] = $val['address'];
	$data['website'] = $val['website'];
	$data['bio_content'] = $val['intro'];
	$data['add_time'] = 514;
	$insert_str = db_arr_to_update_str($data);
	
	$sql = "insert into pai_task_db.task_profile_tbl set ".$insert_str;
	db_simple_getdata($sql,false,101);
	
	$profile_id = db_simple_get_insert_id();
	
	$sql = "update test.task_seller_tbl set status=1,profile_id={$profile_id} where id=".$val['id'];
	db_simple_getdata($sql,false,101);
}

?>