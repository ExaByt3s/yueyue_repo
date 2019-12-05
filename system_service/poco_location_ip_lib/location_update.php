<?php
/**
 * 修改地区
 */
include_once('./location_admin_common.inc.php');

$tpl = new SmartTemplate('location_add.tpl.htm');
$location_conf_obj = new location_conf_class();

$get_lct_id = trim($_GET['lct_id']);
$get_lct_id = (int)$get_lct_id;

$action = $_POST['action'];

if (!empty($get_lct_id))
{
	$get_location_all_list = array();
	$rows = $location_conf_obj->get_location_all_list();
	$location_conf_obj->return_tree_arr($rows, $get_location_all_list);
	$lct_id_option = array();
	$lct_id_option[0] = '省份';
	if (!empty($get_location_all_list) and is_array($get_location_all_list))
	{
		foreach ($get_location_all_list as $key=>$val)
		{
			$lct_space_txt = add_space($val['lv']);
			$lct_id_option[$val['lct_id']] = $lct_space_txt.$val['lct_name'];
		}
		unset($get_location_all_list, $key, $val);
	}
	
	$get_location_info = $location_conf_obj->get_location_info_by_id($get_lct_id);
	
	$tpl->assign($get_location_info);
	$tpl->assign('lct_id_option', $lct_id_option);
	$tpl->assign('lct_id', $get_location_info['lct_fid']);
	
	$tpl->assign('action', 'edit');
	$tpl->assign('no_cache', '?v'.time());
	$tpl->output();
}

if ($action=='edit')
{
	$lct_id = $_POST['lct_id'];
	$lct_icon = trim($_POST['lct_icon']);
	$lct_icon = (int)$lct_icon;
	$lct_name = trim($_POST['lct_name']);
	if ($lct_id=='' or empty($lct_icon) or empty($lct_name))
	{
		$msg = '请完整填写信息';
		$url = 'http://www1.poco.cn/poco_location_ip_lib/location_update.php?lct_id='.$get_lct_id;
		echo "<script type=\"text/javascript\">alert('".$msg."');window.top.location.href='".$href."';</script>";
		exit;
	}
	$update_location['lct_fid'] = $lct_id;
	$update_location['lct_icon'] = $lct_icon;
	$update_location['lct_name'] = $lct_name;
	$update_location = $location_conf_obj->update_location_config($update_location, $get_lct_id);
	if ($update_location)
	{
		$msg = '修改地区成功';
		$url = 'http://www1.poco.cn/poco_location_ip_lib/location_update.php?lct_id='.$get_lct_id;
		echo "<script type=\"text/javascript\">alert('".$msg."');top.location.href='".$href."';</script>";
		exit;
	}
	else
	{
		$msg = '修改地区失败';
		$url = 'http://www1.poco.cn/poco_location_ip_lib/location_update.php?lct_id='.$get_lct_id;
		echo "<script type=\"text/javascript\">alert('".$msg."');window.top.location.href='".$href."';</script>";
		exit;
	}
}
?>