<?php

/**
 * 后台管理 初始化
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-21
 */

// 定义后台路径
defined('PROTOCOL_MASTER_ROOT') || define('PROTOCOL_MASTER_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');
// 定义日志操作类路径
defined('YUE_PROTOCOL_ROOT') || define('YUE_PROTOCOL_ROOT', str_replace('\\', '/', dirname(PROTOCOL_MASTER_ROOT)) . '/');
defined('PROTOCOL_YUEUS_ROOT') || define('PROTOCOL_YUEUS_ROOT', YUE_PROTOCOL_ROOT . 'include/yueus/');
defined('PROTOCOL_CLASS_ROOT') || define('PROTOCOL_CLASS_ROOT', YUE_PROTOCOL_ROOT . 'include/v1/');

require_once(PROTOCOL_MASTER_ROOT . 'master_func.php');  // 引入管理后台公共方法
require_once(YUE_PROTOCOL_ROOT . 'common/function.php');  // 引入协议公共方法
require_once(PROTOCOL_CLASS_ROOT . 'yue_protocol_cache.cls.php');   // 引入协议缓存类
require_once(PROTOCOL_CLASS_ROOT . 'yue_protocol_log.cls.php');   // 引入协议日志类

// 引入 yueus 类
if (!function_exists('yue_master_spl_autoload')) {

    /**
     * 自动加载类
     *
     * @author willike <chenwb@yueus.com>
     * @since 2015-9-23
     */
    function yue_master_spl_autoload($class_name) {
        if (strpos($class_name, 'yue_log_') === 0) {
            $file_path = PROTOCOL_YUEUS_ROOT . strtolower($class_name) . '.cls.php';
            if (file_exists($file_path)) {
                include_once($file_path);
            }
        }
    }
}

if (!defined('YUE_MASTER_AUTOLOAD')) {
    //自动加载类
    define('YUE_MASTER_AUTOLOAD', 1);
    spl_autoload_register('yue_master_spl_autoload');
}

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); // 引入POCO框架


