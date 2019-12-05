<?php

/**
 * 约约 通信 协议
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-11
 * @version 1.0 Beta
 */
// 定义协议的目录
define('YUE_PROTOCOL_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

// 协议处理开始时间 ( 全局 )
$GLOBALS['protocol_start_time'] = microtime(TRUE);

// 引入 通用方法
require YUE_PROTOCOL_ROOT . 'common/function.php';
// 引入 框架主文件
require dirname(YUE_PROTOCOL_ROOT) . '/core/yue.php';   // YUE 框架
require '/disk/data/htdocs233/mypoco/apps/poco_v2/poco.php';  // POCO 框架
// TODO:: 引入协议
POCO::import(YUE_PROTOCOL_ROOT . '/include');
POCO::register('yue_protocol_log');
yue_protocol_log::dump_log('poco_yuepai_android','2015-8-4');

