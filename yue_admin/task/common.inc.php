<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/authority.php');
define('TASK_TEMPLATES_ROOT',"templates/");
define('TASK_DEBUG',true);

if (empty($yue_login_id) || !isset($yue_login_id)) 
{
   	echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Ftask%2Findex.php'</script>";
    exit;
}
$authority_obj  = POCO::singleton('pai_authority_class');
$is_root = $authority_obj->user_id_is_root();
if (!$is_root)
{
	$where_str = "user_id={$yue_login_id} AND module= 'yue_admin' AND action = 'task'";
	$authority_list = $authority_obj->get_authority_list_user('',$where_str);
	if (empty($authority_list)) 
	{
		echo "<script type='text/javascript'>window.alert('你没有权限,请联系管理员获取权限!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Ftask%2Findex.php'</script>";
	    exit;
	}
}
define('TASK_ADMIN_USER_ID',$yue_login_id);
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
$action = $_INPUT['action'];