<?php
/**
 * ²âÊÔ 
 */
include_once('protocol_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
header("Content-type: text/html; charset=utf-8");

function mobile_app_curl($url, $post_data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER,  false);
	curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

$op = trim($_INPUT['op']);
if( $op=='app' )
{
	/*
	$url = 'http://yp.yueus.com/mobile_app/merchant/trade_list.php';
	$param = array(
		'trade_type' => '',
		'location_id' => '',
		'user_id' => 100003,
	);
	$post_data = array(
		'version'   => '2.3.0',
		'os_type'   => 'android',
		'ctime'     => time(),
		'app_name'  => 'poco_yuepai_android',
		'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
		'is_enc'    => 0,
		'param'     => $param,
	);
	$post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);
	print_r(mobile_app_curl($url, $post_data));
	*/
}
elseif( $op=='buy' )
{
	$task_coin_obj = POCO::singleton('pai_task_coin_class');
	
	/*
	$user_id = 110757;
	$amount = 0;
	$coins = 20;
	$subject = 'ÔùËÍÉúÒâ¿¨';
	$more_info = array('remark'=>'');
	$submit_ret = $task_coin_obj->submit_buy($user_id, $amount, $coins, $subject, $quotes_id=0, $more_info);
	var_dump($submit_ret);
	
	$buy_info = $task_coin_obj->get_buy_info($submit_ret['buy_id']);
	var_dump($buy_info);
	
	$ret = $task_coin_obj->pay_buy($buy_info);
	var_dump($ret);
	*/
}
else
{
	die('op error');
}
