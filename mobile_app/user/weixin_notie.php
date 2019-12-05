<?php
/**
 * 通知：只使用微信公众号的消费者，收不到私信
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$from_send_id = (int)$_INPUT['from_send_id'];
$to_send_id = (int)$_INPUT['to_send_id'];

$cache_key = "PAI_WEIXIN_NOTFY_CACHE_".$to_send_id;

$sql = "insert into pai_log_db.tmp_weixin_send_log set send_user_id=$from_send_id ,to_user_id=$to_send_id";
db_simple_getdata($sql,false,101);

//微信通知
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
//获取商家信息
$seller_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $seller_obj->get_seller_info($from_send_id, 2);

$cache = POCO::getCache ( $cache_key );

if( !empty($seller_info) && !$cache)
{
	$user_id = $to_send_id;
	$template_code = 'G_PAI_WEIXIN_MT_CHAT';
	$nickname = get_user_nickname_by_user_id($from_send_id);
	$data = array ('nickname' => $nickname,'user_id'=>$from_send_id );
	$to_url = 'http://app.yueus.com/';
	$ret = $weixin_pub_obj->message_template_send_by_user_id ( $user_id, $template_code, $data, $to_url );
	
	$cache_time = 3600;
	POCO::setCache ( $cache_key, 1, array ('life_time' => $cache_time ) );
}
?>