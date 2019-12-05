<?php 

	include_once 'common.inc.php';
	$user_obj = POCO::singleton('pai_user_class');
	$user_obj->logout();
	echo "<script type='text/javascript'>window.alert('ÍË³ö³É¹¦!');location.href='../index.php'</script>";
	exit;

 ?>