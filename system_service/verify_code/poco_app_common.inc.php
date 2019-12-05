<?
/**
 * 应用框架项目公共文件
 */

//应用项目对象声明
global $myAppVerifyCode;
//引入应用项目配置信息
$app_config = require(dirname(__FILE__) . '/config/app_config.php');
//引入应用框架主文件
require $app_config['POCO_APP_DIR'] . '/poco.php';
//引入应用项目程序初始化类
require dirname(__FILE__) . '/include/poco_app_verify_code.inc.php';
//启动应用程序并返回应用程序对象唯一实例
$myAppVerifyCode = POCO_APP_VERIFY_CODE::instance($app_config);

define('G_YUEYUE_VERIFY_CODE_ROOT_PATH', realpath(dirname(__FILE__)));
