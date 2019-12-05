<?php 

/*
 *退出
*/
	include_once 'common.inc.php';
	$user_obj = POCO::singleton('pai_user_class');
	$user_obj->logout();
	echo "<script type='text/javascript'>window.alert('退出成功!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Fcoupon%2Findex.php'</script>";
	exit;

 ?>