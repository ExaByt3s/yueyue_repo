<?php 

/*
 *退出
*/
	include_once 'common.inc.php';
	$user_obj = POCO::singleton('pai_user_class');
	$user_obj->logout();
	echo "<script type='text/javascript'>window.alert('退出成功!');location.href='login.php'</script>";
	exit;

 ?>