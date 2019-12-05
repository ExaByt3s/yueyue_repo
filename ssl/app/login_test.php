<?php
/*
 * µÇÂ¼ÑéÖ¤
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$req = $_INPUT['req'];
$client_data = json_decode($req,true);

$phone = $client_data  ['param'] ['phone'];
$pwd = $client_data  ['param'] ['pwd'];


$pai_user_obj = POCO::singleton ( 'pai_user_class' );

$user_id = $pai_user_obj->user_login($phone, $pwd);


$log_arr['req'] = $req;
$log_arr['client_data'] = $client_data;
pai_log_class::add_log($log_arr, 'ssl_login_test', 'ssl_login_test');

if($user_id)
{
	json_msg(1, 'µÇÂ½³É¹¦');
}
else
{
	json_msg(0, 'ÕËºÅ»òÃÜÂë´íÎó');;
}


?>

