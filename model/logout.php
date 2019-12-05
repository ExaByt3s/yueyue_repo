<?php
/** 
 * 
 * Ô¼Ô¼ ÍË³öµÇÂ¼
 * 
 * author hudw
 * 
 * 
 * 2015-1-23
 * 
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$pai_user_obj = POCO::singleton('pai_user_class');
$ret = $pai_user_obj->logout();

echo "<script>top.location.href='http://www.yueus.com/model/login.php'</script>";

?>