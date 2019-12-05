<?php
/**
 * 获取授权链接
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$url = trim($_INPUT['url']);
$url2 = trim($_INPUT['url2']);
$scope = trim($_INPUT['op']);
if( strlen($scope)<1 ) $scope = 'snsapi_base';

$params = array(
	'url' => $url,
	'url2' => $url2,
);
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
$auth_url = $weixin_pub_obj->auth_get_authorize_url($params, $scope);

$_GET['auth_url'] = $auth_url;

//临时日志
$payment_obj = POCO::singleton('pai_payment_class');
ecpay_log_class::add_log($_GET, __FILE__ , 'pai_weixin_auth_act');

$return_arr['result'] = 200;
$return_arr['msg'] = '请先到微信授权';
$return_arr['auth_url'] = $auth_url;
mobile_output($return_arr, false);
