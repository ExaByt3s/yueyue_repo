<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton('pai_user_class');

$user_obj->logout();

header("Location: login.php");
?>