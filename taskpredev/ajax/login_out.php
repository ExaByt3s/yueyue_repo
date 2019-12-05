<?php

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
global $yue_login_id;


$user_obj = POCO::singleton ( 'pai_user_class' );

$user_obj->logout();

?>