<?php

/**
 * config for protocol master manager system
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-22
 */
defined('PROTOCOL_MASTER_ROOT') or die('----------conf error----------');

return array(
    // 允许登陆的用户列表
    'LOGIN_ACCESS' => array(
        117452,  // willike
        100003,  // 汉裕
        100022,  // 安
        108559,  // 老肖
        130968,  // 周颖
        100293,  // 小肖
        109650,  // koko
        119220,  // 子皓
        100009,  // 黄文清
        100087,  // 冠龙
        214380,  // 彭涛
        110371,  // ios 守富
        132417,  // 罗珍杨
    ),
    // 管理权限 ( 查看统计 )
    'MASTER_ADMIN' => array(
        117452,  // 伟标
        100008,  // 华哥
        100002,  // bowie
    ),
    // 安全
    'ADMIN_ACCESS_ACTION' => array('expo', 'postman', 'list', 'handler'),  // 需要管理员权限
    'NO_LOGIN_REQUIRED_ACTION' => array('login', 'error', 'detect'),  // 不需要登录权限 访问
    'DATA_AUTH_KEY' => 'willike@YUEUS.2015',  // 密钥 (慎改)
    'SINGLE_LOGIN' => true,  // 单点登录
    'ONESELF_API' => array('_edit', 'seller_mass'), // 仅支持调试当前(自己)用户 的接口
    // COOKIE 配置
    'COOKIE_EXPIRE' => (7 * 24 * 60 * 60), // cookie 有效期 (7天)
    'COOKIE_DOMAIN' => filter_input(INPUT_SERVER, 'HTTP_HOST'), // Cookie有效域名
    'COOKIE_PATH' => '/protocol/master/', // Cookie路径
    // 其他配置
    'DEFAULT_API_VERSION' => array( // 接口调试默认版本
    	'customer' => '3.2.0',
    	'merchant' => '1.2.0',
	), 
    'META_TITLE' => array(
        'api' => '接口调试',
        'detect' => '脚本检测',
        'error' => '页面错误',
        'index' => '首页',
        'info' => '提示',
        'list' => '日志列表',
        'login' => '登录',
        'postman' => 'POSTMAN更新',
        'search' => '输入查询',
        'handler' => '协议HANDLER错误',
    ),
);

