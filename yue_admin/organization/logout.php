<?php 

/*
 *�˳�
*/
	include_once 'common.inc.php';
	$user_obj = POCO::singleton('pai_user_class');
	$user_obj->logout();
	echo "<script type='text/javascript'>window.alert('�˳��ɹ�!');location.href='login.php'</script>";
	exit;

 ?>