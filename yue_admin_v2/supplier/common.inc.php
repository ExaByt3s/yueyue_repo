<?php
include_once('../common.inc.php');
define('SUPPLIER_TEMPLATES_ROOT',"templates/");
$mall_supplier_obj  = POCO::singleton('pai_mall_supplier_class');

if (empty($yue_login_id) || !isset($yue_login_id))
{
	echo "<script type='text/javascript'>window.top.location.href='login.php';</script>";
	exit;
}
else
{
	$supplier = $mall_supplier_obj->get_supplier_info_by_id($yue_login_id);
	if (!$supplier)
	{
		echo "<script type='text/javascript'>window.alert('您没有该模块权限');top.location.href='login.php';</script>";
		exit;
	}
}
define('SUPPLIER_ADMIN_USER_ID',intval($yue_login_id));

$action = $_INPUT['action'];
?>