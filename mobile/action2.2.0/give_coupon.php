<?php

/**
 * 获取可用优惠券
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	$output_arr['list'] = array();
	$output_arr['code'] = 0;

	mobile_output($output_arr,false);

	die();
} 
	
/**
 * 页面接收参数
 */
$sn = $_INPUT['sn'];

$coupon_obj = POCO::singleton('pai_coupon_class');

$ret = $coupon_obj->give_coupon($yue_login_id,$sn,$b_valid=true);

$output_arr['list'] = $ret;

if($ret['result'] !=1)
{
	$output_arr['code'] = 0;
}
else
{
	$output_arr['code'] = 1;
}



mobile_output($output_arr,false);

?>