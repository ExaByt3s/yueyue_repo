<?php 
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$user_obj = POCO::singleton ( 'pai_user_class' );
$user_obj->logout();
echo "<script type='text/javascript'>window.alert('ÍË³ö³É¹¦!');location.href='login.php'</script>";
exit;

?>