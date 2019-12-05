<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$_COOKIE['yue_seller_admin'])
{
	$output_arr['message'] = '超时操作';
	$output_arr['code'] = -1;
	mobile_output($output_arr,false); 
	exit;
}

$payment_obj = POCO::singleton('pai_payment_class');

$card_no = $_INPUT['card_no'];
$type = $_INPUT['type'];

if(!in_array($type,array("active","disable")))
{
	$output_arr['message'] = '非法操作';
	$output_arr['code'] = 0;
	mobile_output($output_arr,false); 
	exit;
}

switch ($type) {
	case "active":
		$ret = $payment_obj->enable_card($yue_login_id, $card_no);
	break;
	
	case "disable":
		$ret = $payment_obj->disable_card($yue_login_id, $card_no);
	break;
}

$log_arr['result'] = $ret;
pai_log_class::add_log($log_arr, 'seller_admin', 'seller_admin');

if($ret['result']==1)
{
	$output_arr['code'] = 1;
}
else
{
	$output_arr['code'] = 0;
}

$output_arr['message'] = $ret['message'];
mobile_output($output_arr,false); 

?>