<?php
include_once('../common.inc.php');
include_once('config/authority.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/authority.php');
if (empty($yue_login_id) || !isset($yue_login_id)) 
{
   	header("location:../index.php");
    exit;
}

/*if ($yue_login_id != 100293) 
{
	echo "程序正在修改中请稍等片刻!";
	exit;
	# code...
}*/
$authority_obj  = POCO::singleton('pai_authority_class');
$where_str = "user_id={$yue_login_id} AND module= 'yue_admin'";
$authority_list = $authority_obj->get_authority_list_user('',$where_str);




?>