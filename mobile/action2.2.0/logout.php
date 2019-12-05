<?php
/**
 * hudw 2014.9.16
 * 退出登录
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_id = $_INPUT['user_id'];


/*if($user_id != $yue_login_id)
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = '非法退出';
	mobile_output($output_arr,false);
	exit();
}*/

$nfc_obj = POCO::singleton('pai_nfc_class');
$nfc_obj->mobile_logout($yue_login_id);

$pai_user_obj = POCO::singleton('pai_user_class');
$ret = $pai_user_obj->logout();


$output_arr['code'] = $ret;
$output_arr['msg'] = '退出登录';


mobile_output($output_arr,false);

?>