<?php
include_once('../common.inc.php');
echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin_v2/organization';</script>";
exit;
$organization_obj  = POCO::singleton('pai_organization_class');
if (empty($yue_login_id) || !isset($yue_login_id)) 
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
/*$authority_obj  = POCO::singleton('pai_authority_class');
$where_str = "user_id={$yue_login_id} AND module= 'yue_admin' AND action = 'organization'";
$authority_list = $authority_obj->get_authority_list_user('',$where_str);
if (empty($authority_list)) 
{
	echo "<script type='text/javascript'>window.alert('你没有权限,请联系管理员获取权限!');location.href='login.php'</script>";
    exit;
}*/
//print_r($authority_list);exit;




?>