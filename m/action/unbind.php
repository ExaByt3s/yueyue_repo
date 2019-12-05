<?php
/**
 * hudw 2014.9.16
 * 解绑
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
$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');

$bind_weixin_obj->delete_user($yue_login_id);

$ret = $pai_user_obj->logout();

//退出授权
setcookie('yueus_openid', '', null, '/', '.yueus.com');
setcookie('yueus_code', '', null, '/', '.yueus.com');
setcookie('yueus_scope', '', null, '/', '.yueus.com');
setcookie('yueus_url2', '', null, '/', '.yueus.com'); //已授权、未绑定、未登录，将url2传递给前端，以便前端注册、登录完跳转

$output_arr['code'] = $ret;
$output_arr['msg'] = '退出登录';


mobile_output($output_arr,false);

?>