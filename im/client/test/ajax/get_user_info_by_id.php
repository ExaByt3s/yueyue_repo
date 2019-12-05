<?php
include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$id_arr = $_INPUT['id_arr'];
foreach ($id_arr as $k=>$value) {
  $icon = get_seller_user_icon($value);
  $name = get_seller_nickname_by_user_id($value);
  $ret[$k]['id'] = $value;
  $ret[$k]['icon'] = $icon;
  $ret[$k]['name'] = $name;
};


$output_arr = $ret;

mobile_output($output_arr,false);

?>