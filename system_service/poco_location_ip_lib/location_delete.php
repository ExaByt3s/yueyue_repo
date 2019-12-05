<?php
/**
 * 删除地区
 */
include_once('./location_admin_common.inc.php');

$location_conf_obj = new location_conf_class();

$get_lct_id = trim($_GET['lct_id']);
$get_lct_id = (int)$get_lct_id;

if (!empty($get_lct_id))
{
	$del_location = $location_conf_obj->del_location_config($get_lct_id);
	if ($del_location)
	{
		$msg = '删除地区成功';
		$url = 'http://www1.poco.cn/poco_location_ip_lib/location_list.php';
		echo "<script type=\"text/javascript\">alert('".$msg."');window.location.href='".$href."'</script>";
		exit;
	}
}
else
{
	$msg = '删除地区失败';
	$url = 'http://www1.poco.cn/poco_location_ip_lib/location_list.php';
	echo "<script type=\"text/javascript\">alert('".$msg."');window.location.href='".$href."'</script>";
	exit;
}
?>