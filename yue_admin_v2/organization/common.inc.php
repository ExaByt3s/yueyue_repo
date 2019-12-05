<?php

/**
 * @desc:   权限页
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/6
 * @Time:   11:30
 * version: 2.0
 */
define('YUE_LOGIN_ORGANIZATION',1);
include_once('../common.inc.php');
$organization_obj  = POCO::singleton('pai_organization_class');
$user_id = intval($yue_login_id);

if ($user_id<1)
{
	 echo "<script type='text/javascript'>window.top.location.href='login.php';</script>";
	 exit;
}
else
{
	$id = $organization_obj->get_org_id_by_user_id_v2($yue_login_id,1);
	if (!$id) 
     {
       	echo "<script type='text/javascript'>window.alert('您没有该模块权限');top.location.href='login.php';</script>";
        exit;
     }
}




?>