<?php

//定义默认cookie域，例如用于登录、退出
if( preg_match('/(^|\.)yueus\.com$/isU', $_SERVER['SERVER_NAME']) )
{
	define('G_LOGIN_SESSION_PARTICULAR_COOKIE_DOMAIN', 'yueus.com');
}

// 因为现在有些头像显示不出，要查问题，所以去掉cdn先,测试完就会去掉 
//define('G_USER_ICON_DONT_USE_CDN_URL',1);

// 支持 REST
$method = $_SERVER['REQUEST_METHOD']; 
if ($method == 'PUT' || $method == 'DELETE') {
    parse_str(file_get_contents('php://input'), $params);
    $GLOBALS['_'.$method] = $params;

    foreach ($params as $key=>$val) {
        $_REQUEST[$key] = $val;
        $HTTP_GET_VARS[$key] = $val;
        //$HTTP_POST_VARS[$key] = $val;
    }
}
unset($method, $params, $key, $val);

if(extension_loaded('zlib'))
{
	//检查服务器是否开启了zlib拓展
    ob_start('ob_gzhandler');
}



include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/include/pai_function.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
include_once("/disk/data/htdocs232/poco/pai/mobile/include/ubb_fun.php");

include_once('/disk/data/htdocs232/poco/pai/enroll_act.php');
include_once('/disk/data/htdocs232/poco/pai/event_act.php');
include_once('/disk/data/htdocs232/poco/pai/date_act.php');



$GLOBALS['pai_app'] = $my_app_pai;

$action_version = str_replace('/mobile/','',$_SERVER['REDIRECT_SCRIPT_URL']);

//define('G_PAI_ECPAY_DEV',1);

define('G_PAI_APP_VERSION','webbeta');



if (!defined('G_PAI_APP_PATH')) 
{
    define('G_PAI_APP_PATH', realpath(dirname(__FILE__)).'/');

}

/**
 * 域名控制
 */
//define('G_PAI_APP_DOMAIN', 'http://pai.poco.cn');
define('G_PAI_APP_DOMAIN', 'http://yp.yueus.com');


// 测试使用账户 hudw 2014.11.4
define('TEST_USER_ACCOUNT',serialize(array(175321481,173718999,173718999,66096046)));
//海todo 现使用yue_login_id  这里以后可以要改成用yue_login_id 而不是login_id
if ($yue_login_id>0)
{
	define('G_DB_GET_REALTIME_DATA', 1);// 如果是当前用户，实时数据
}

//error_reporting(15);
?>