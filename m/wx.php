<?php
/**
 * 中间入口页，为了能及时的更新版本
 * @author Henry
 * @copyright 2015-01-16
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$mode = trim($_GET['mode']);
$route = trim($_GET['route']);
$url = trim($_GET['url']);

$params = array();
if( strlen($mode)>0 ) $params['mode'] = $mode;
if( strlen($route)>0 ) $params['route'] = $route;
if( strlen($url)>0 ) $params['url'] = $url;

$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
$auth_url = $weixin_pub_obj->auth_get_authorize_url($params, 'snsapi_base');

//日志
$payment_obj = POCO::singleton('pai_payment_class');
ecpay_log_class::add_log($auth_url, 'code', 'pai_weixin_wx');

header('Location:' . $auth_url);
