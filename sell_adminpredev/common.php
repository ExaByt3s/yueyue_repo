<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton('pai_user_class');

if(!$_COOKIE['yue_seller_admin'])
{
	
	$user_obj->logout();
	
	header("Location: login.php");
	exit;
}

$payment_obj = POCO::singleton('pai_payment_class');
$page_obj = new show_page ();

$check_seller = $payment_obj->get_card_seller_info($yue_login_id);

if(!$check_seller)
{
	js_pop_msg("该账号不是商家账号",false,"login.php");
}


$check_is_card_seller = $payment_obj->check_is_card_seller($yue_login_id);

if(!$check_is_card_seller)
{
	$user_obj->logout();
	js_pop_msg("该账号已停用",false,"login.php");
}

setcookie("yue_seller_admin", 1, time()+600, "/", "yueus.com");

?>