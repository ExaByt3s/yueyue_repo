<?php
include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$arr = $_INPUT['arr'];

foreach ($arr as $k=>$value) {
	if($value['role'] == 'yuebuyer')
	{
		$icon = get_user_icon($value['id']);
		$name = get_user_nickname_by_user_id($value['id']);
		$ret[$k]['user_id'] = $value['id'];
		$ret[$k]['icon'] = $icon;
		$ret[$k]['name'] = $name;
		$ret[$k]['role'] = 'yuebuyer';
	}
	else
	{
		$icon = get_seller_user_icon($value['id']);
		$name = get_seller_nickname_by_user_id($value['id']);
		$ret[$k]['user_id'] = $value['id'];
		$ret[$k]['icon'] = $icon;
		$ret[$k]['name'] = $name;
		$ret[$k]['role'] = 'yueseller';
	}
};
  
mobile_output($ret,false);

?>