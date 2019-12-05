<?php
/**
 * 分享优惠券
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$obj = POCO::singleton ( 'pai_event_coupon_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class'); 
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );

/**
 * 页面接收参数
 */
$event_id = intval($_INPUT['event_id']);
$url = $_INPUT['url'];
$cellphone = $_INPUT['cellphone'];

$version_control = include('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
$cache_ver = trim($version_control['wx']['cache_ver']);



$output_arr['code'] = 1;


mobile_output($output_arr,false);


?>