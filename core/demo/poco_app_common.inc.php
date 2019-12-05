<?
/**
 * 应用框架项目公共文件
 */

//应用项目对象声明
global $my_app_demo;
//引入应用项目配置信息
$app_demo_config = require(dirname(__FILE__) . '/config/app_config.php');
//引入应用框架主文件
require $app_demo_config['POCO_APP_DIR'] . '/poco.php';
//引入应用项目程序初始化类
require dirname(__FILE__) . '/include/poco_app_demo.inc.php';
//启动应用程序并返回应用程序对象唯一实例
$my_app_demo = POCO_APP_DEMO::instance($app_demo_config);
