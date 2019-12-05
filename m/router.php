<?php


// 约拍重定向
$SCRIPT_URL = trim($_SERVER['SCRIPT_URL']);

$router_arr = explode('/',trim($SCRIPT_URL));

$route = $router_arr[2];



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
    // 微信模式
    case 'wx':
        define('G_PAI_APP_PAGE_MODE', 'wx');
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

    include('/disk/data/htdocs232/poco/pai/m/'.$script_uri);    
}
else
{
    header("HTTP/1.1 404 not found");    
}


exit();
?>