<?php

/**
 * 约约 通信 协议
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-29
 * @version 1.0 Beta
 */
// 定义协议的目录
define('YUE_PROTOCOL_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

// 协议处理开始时间 ( 全局 )
$GLOBALS['protocol_start_time'] = microtime(TRUE);

// 引入 通用方法
require YUE_PROTOCOL_ROOT . 'common/function.php';

$v = conf('PROTOCOL_VERSION', 'config');   // 协议版本
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_request.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_response.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_oauth.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_log.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_cache.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_system.cls.php');

//// 引入 yue协议类
//if (!function_exists('yue_protocol_spl_autoload')) {
//
//    /**
//     * 自动加载类
//     * 
//     * @author willike <chenwb@yueus.com>
//     * @since 2015-7-30
//     */
//    function yue_protocol_spl_autoload($class_name) {
//        if (strpos($class_name, 'yue_protocol_') === 0) {
//            $file_path = YUE_PROTOCOL_ROOT . 'include/' . strtolower($class_name) . '.cls.php';
//            if (file_exists($file_path)) {
//                include_once($file_path);
//            }
//        }
//    }
//
//}
//
//if (!defined('YUE_PROTOCOL_AUTOLOAD')) {
//    //自动加载类
//    define('YUE_PROTOCOL_AUTOLOAD', 1);
//    spl_autoload_register('yue_protocol_spl_autoload');
//}

// 兼容旧协议, 定义类别名,  PHP > 5.3
//class_alias('yue_protocol_system','poco_communication_protocol_class');
