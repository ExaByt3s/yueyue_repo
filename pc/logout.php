<?php
/** 
 * 
 * 2015-3-2
 * 
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$pai_user_obj = POCO::singleton('pai_user_class');
$ret = $pai_user_obj->logout();

$user_agent_arr = mall_get_user_agent_arr();

if($user_agent_arr['is_weixin'])
{
    $bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
    $bind_weixin_obj->delete_user($yue_login_id);
}

$referer = trim($_SERVER['HTTP_REFERER']);
if( strlen($referer)<1 ) $referer = 'http://www.yueus.com/';
echo "<script>location.href='{$referer}'</script>";

?>