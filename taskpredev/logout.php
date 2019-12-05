<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$user_obj = POCO::singleton ( 'pai_user_class' );

$user_obj->logout();

header("Location: http://www.yueus.com/reg/login.php?r_url=http%3A%2F%2Fwww.yueus.com%2Ftask%2F"); 

?>