<?php


// 约拍重定向
$SCRIPT_URL = trim($_SERVER['SCRIPT_URL']);

$router_arr = explode('/',trim($SCRIPT_URL));

$route = $router_arr[2];

// wx jump
$wx_query = strstr($_SERVER['REDIRECT_QUERY_STRING'],'preview');
if($_SERVER['SCRIPT_URI'] == 'http://yp.yueus.com/mobile/webtest' && !$wx_query)
{
	//兼容处理，微信正点女神专题
	include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

	$version_control = include('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
	$url = 'http://yp.yueus.com/m/wx?v'.$version_control['wx']['cache_ver'].'#account/register/reg';
	$url2 = 'http://yp.yueus.com/m/wx?v'.$version_control['wx']['cache_ver'].'#topic/57';
	$params = array(
		'url' => $url,
		'url2' => $url2,
	);
	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
	$auth_url = $weixin_pub_obj->auth_get_authorize_url($params, 'snsapi_userinfo');
	
    header('Location:' . $auth_url);
    die();
}




switch ($route) 
{
	// 页面开发模式
    case 'webdev':
        define('G_PAI_APP_PAGE_MODE', 'dev');
        define('G_PAI_APP_PAGE_SCENE', 'web');
        $script_uri = 'script.php';
        break;

    // 页面测试模式
    case 'webbeta':
        define('G_PAI_APP_PAGE_MODE', 'beta');
        define('G_PAI_APP_PAGE_SCENE', 'web');
        $script_uri = 'script.php';
        break;
    // 页面测试模式 test
    case 'webtest':
        define('G_PAI_APP_PAGE_MODE', 'test');
        define('G_PAI_APP_PAGE_SCENE', 'web');
        $script_uri = 'script.php';
        break;        

    // app开发模式
    case 'appdev':
        define('G_PAI_APP_PAGE_MODE', 'dev');
        define('G_PAI_APP_PAGE_SCENE', 'app');
        $script_uri = 'script.php';
        break;

    // app测试模式
    case 'appbeta':
        define('G_PAI_APP_PAGE_MODE', 'beta');
        define('G_PAI_APP_PAGE_SCENE', 'app');
        $script_uri = 'script.php';
        break;

    // app线上模式
    case 'app':
        define('G_PAI_APP_PAGE_MODE', 'archive');
        define('G_PAI_APP_PAGE_SCENE', 'app');
        $script_uri = 'script.php';
        break;

    // web线上模式
    case 'index.html':
        define('G_PAI_APP_PAGE_MODE', 'archive');
        define('G_PAI_APP_PAGE_SCENE', 'web');   
        $script_uri = 'script.php';
        break;
}


/**
 * 引入文件处理
 */


if($script_uri)
{
    header('HTTP/1.0 200 OK');

    include('/disk/data/htdocs232/poco/pai/mobile/'.$script_uri);    
}
else
{
    header("HTTP/1.1 404 not found");    
}



exit();
?>