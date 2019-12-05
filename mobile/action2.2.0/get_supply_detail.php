<?php
/**
 * ╗ё╡├╦╤╦ў▒ъ╟й
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$coupon_obj = POCO::singleton('pai_coupon_class');

$supply_id = $_INPUT['supply_id'];


$ret = $coupon_obj -> get_supply_detail($supply_id);

$info = $coupon_obj -> get_supply_user_info($supply_id,$yue_login_id);
if( empty($info) )
{
	$ret['give_status'] = -1;
}
elseif($info['is_give']==1)
{
	$ret['give_status'] = 1;
	$ret['coupon_sn'] = trim($info['coupon_sn']);
}
else
{
	$ret['give_status'] = 0;
}

$ret['scope_module_type_name'] = 'б╕'.$ret['scope_module_type_name'].'б╣';
$ret['scope_order_total_amount'] = $ret['scope_order_total_amount']*1;
$ret['face_value'] = $ret['face_value']*1;
$ret['coin'] = 'гд'; 

$output_arr = $ret;

mobile_output($output_arr,false);

?>