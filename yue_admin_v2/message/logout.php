<?php 

/*
 *退出
*/
	include_once 'common.inc.php';
	$user_obj = POCO::singleton('pai_user_class');
	$user_obj->logout();
	echo "<script type='text/javascript'>window.alert('退出成功!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3a%2f%2fyp.yueus.com%2fyue_admin_v2%2fmessage%2findex.php'</script>";
	exit;

 ?>