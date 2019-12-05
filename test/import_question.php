<?php 
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "select * from test.task_seller_tbl";
$seller_arr = db_simple_getdata($sql,false,101);

foreach($seller_arr as $val)
{
	if($val['q1'])
	{
		$data['profile_id'] = $val['profile_id'];
		$data['title'] = "告诉我你和其他同行最大的不同是什么？";
		$data['content'] = $val['q1'];
		$data['add_time'] = 1234567;
		
		$insert_str = db_arr_to_update_str($data);
		$sql = "insert into pai_task_db.task_profile_faq_tbl set ".$insert_str;
		db_simple_getdata($sql,false,101);
	}
	
	if($val['q2'])
	{
		$data['profile_id'] = $val['profile_id'];
		$data['title'] = "告诉我最有趣/难忘的的一次服务经历吧";
		$data['content'] = $val['q2'];
		$data['add_time'] = 1234567;
		
		$insert_str = db_arr_to_update_str($data);
		$sql = "insert into pai_task_db.task_profile_faq_tbl set ".$insert_str;
		db_simple_getdata($sql,false,101);
	}
	
	if($val['q3'])
	{
		$data['profile_id'] = $val['profile_id'];
		$data['title'] = "你觉得你会怎样改进你提供的服务？";
		$data['content'] = $val['q3'];
		$data['add_time'] = 1234567;
		
		$insert_str = db_arr_to_update_str($data);
		$sql = "insert into pai_task_db.task_profile_faq_tbl set ".$insert_str;
		db_simple_getdata($sql,false,101);
	}
	
	if($val['q4'])
	{
		$data['profile_id'] = $val['profile_id'];
		$data['title'] = "如果有奖项给我看看吧";
		$data['content'] = $val['q4'];
		$data['add_time'] = 1234567;
		
		$insert_str = db_arr_to_update_str($data);
		$sql = "insert into pai_task_db.task_profile_faq_tbl set ".$insert_str;
		db_simple_getdata($sql,false,101);
	}
	
	if($val['q5'])
	{
		$data['profile_id'] = $val['profile_id'];
		$data['title'] = "还有什么想告诉我的";
		$data['content'] = $val['q5'];
		$data['add_time'] = 1234567;
		
		$insert_str = db_arr_to_update_str($data);
		$sql = "insert into pai_task_db.task_profile_faq_tbl set ".$insert_str;
		db_simple_getdata($sql,false,101);
	}
}
?>