<?php

/*
 * 全站配置
*/
include_once('../common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/authority.php');
if (empty($yue_login_id) || !isset($yue_login_id)) 
{
   echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Fauthority%2Findex.php'</script>";
    exit;
}
$authority_obj  = POCO::singleton('pai_authority_class');
$authority_list = $authority_obj->user_id_is_root();
if(!$authority_list)
{
	$where_str = "user_id={$yue_login_id} AND module= 'yue_admin' AND action='authority' ";
	$authority_list = $authority_obj->get_authority_list_user('',$where_str);
	if (empty($authority_list) && is_array($authority_list)) 
	{
		echo "<script type='text/javascript'>window.alert('你没有权限,请联系管理员获取权限!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Fauthority%2Findex.php'</script>";
		exit;
	}
}






?>