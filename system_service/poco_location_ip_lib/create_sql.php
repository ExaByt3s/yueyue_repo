<?php
/**
 * 批量增加地区
 */
include_once('./location_admin_common.inc.php');

$tpl = new SmartTemplate('create_sql.tpl.htm');
$location_conf_obj = new location_conf_class();

$button = $_POST['button'];
if (!empty($button))
{
	$input_txt = $_POST['input_txt'];
	$str = explode("\n", $input_txt);
	
	foreach ($str as $key=>$val)
	{
		$father_lct_icon = substr($val, 0, 6);
		$get_type_info = $location_conf_obj->get_type_info_by_icon($father_lct_icon);
		if (empty($get_type_info) and is_array($get_type_info))
		{
			$str_2 = explode("\t", $val);
			$add_location_arr['lct_fid'] = 0;
			$add_location_arr['lct_icon'] = substr($str_2[0], 0, 6);
			$add_location_arr['lct_name'] = trim($str_2[1]);
			$add_location = $location_conf_obj->add_location_config($add_location_arr);
			unset($add_location_arr);
			$add_location_arr['lct_fid'] = $add_location;
			$add_location_arr['lct_icon'] = trim($str_2[0]);
			$add_location_arr['lct_name'] = trim($str_2[2]);
			$add_location = $location_conf_obj->add_location_config($add_location_arr);
		}
		else 
		{
			$str_2 = explode("\t", $val);
			$add_location_arr['lct_fid'] = $get_type_info['lct_id'];
			$add_location_arr['lct_icon'] = trim($str_2[0]);
			$add_location_arr['lct_name'] = trim($str_2[2]);
			$add_location = $location_conf_obj->add_location_config($add_location_arr);
		}
	}
	$insert_sql = $str;
}
else 
{
	$insert_sql = '';
}
print_r($insert_sql);
$tpl->assign('insert_sql', $insert_sql);

$tpl->output();
?>