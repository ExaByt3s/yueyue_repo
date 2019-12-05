<?php
/**
 * ╗ё╡├╦╤╦ў▒ъ╟й
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$coupon_obj = POCO::singleton('pai_coupon_class');

$supply_id = $_INPUT['supply_id'];

$ret = $coupon_obj -> give_supply_coupon($supply_id,$yue_login_id);

//$ret['scope_module_type_name'] = 'б╕'.$ret['scope_module_type_name'].'б╣';
//$ret['scope_order_total_amount'] = intval($ret['scope_order_total_amount']);
//$ret['face_value'] = intval($ret['face_value']);
//$ret['coin'] = 'гд'; 

$output_arr = $ret;

mobile_output($output_arr,false);

?>