<?php
include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton('pai_user_class');

$ret = $user_obj->logout();

mobile_output($ret,false);

?>