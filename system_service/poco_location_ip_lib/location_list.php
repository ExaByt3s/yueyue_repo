<?php
/**
 * 地区列表
 */
include_once('./location_admin_common.inc.php');

$tpl = new SmartTemplate('location_list.tpl.htm');
$location_conf_obj = new location_conf_class();

$get_location_all_list = array();
$rows = $location_conf_obj->get_location_all_list();
$location_conf_obj->return_tree_arr($rows, $get_location_all_list);

if (!empty($get_location_all_list) and is_array($get_location_all_list))
{
	foreach ($get_location_all_list as $key=>$val)
	{
		$lct_space_txt = add_space($val['lv']);
		if ($val['lv']==0)
		{
			$get_location_all_list[$key]['show_tr'] = 'block';
			$get_location_all_list[$key]['lct_icon_txt'] = $val['lct_icon'];
		}
		else
		{
			$get_location_all_list[$key]['lct_icon_txt'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|'.$lct_space_txt.$val['lct_icon'];
			$get_location_all_list[$key]['show_tr'] = 'none';
		}
		$get_location_all_list[$key]['lct_name_txt'] = $lct_space_txt.$val['lct_name'];
	}
	unset($key, $val);
}
$tpl->assign('location_list', $get_location_all_list);
$tpl->assign('no_cache', '?v'.time());
$tpl->output();
?>