<?php 
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton ( 'pai_user_class' );

$user_obj->logout();

echo "<script>top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fwww.yueus.com%2Fyue_admin%2Foperate_agent%2F';</script>";
?>