<?php

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$bind_weixin_obj = POCO::singleton ( 'pai_bind_weixin_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );

$sql = "select * from pai_task_db.task_profile_tbl where add_time=514";
$user_arr = db_simple_getdata ( $sql, false, 101 );

foreach ( $user_arr as $val ) {
	
	$cellphone = $val['cellphone'];
	$user_id = $val['user_id'];
	//echo $cellphone."<br />";
	$file = "/disk/data/htdocs232/poco/pai/test/tt_icon/icon3/{$cellphone}.jpg";
	$exist = file_exists ( $file );
	//echo $file."<br />";
	if($exist)
	{
		echo $cellphone."<br />";
		$arr = $bind_weixin_obj->upload_icon ( $user_id, "http://www.yueus.com/test/tt_icon/icon3/{$cellphone}.jpg" );
	
		print_r ( $arr );
	}
	
	

}

?>