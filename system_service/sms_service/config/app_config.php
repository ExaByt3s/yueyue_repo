<?php
/**
 * 应用程序基本启动文件，提供应用程序运行的关键设置信息
 */

$root_dir = dirname(dirname(__FILE__));

/**
 * 如果要集成第三方的 PHP 库，错误报告也许要修改为：
 *
 * error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
 */
//error_reporting(E_ALL | E_STRICT);

/**
 * 应用程序配置信息
 */
return array(

    /**
     * 应用框架所在目录
     */
    'POCO_APP_DIR'          => "/disk/data/htdocs232/poco/pai/core",

    /**
     * 数据库服务器ID
     */
    'SERVER_ID'             => false,
    
    /**
     * 数据库名称
     */
    'DB_NAME'               => '',

    /**
     * 应用程序的名称
     */
    'APP_NAME'              => 'sms_service',
    
    /**
     * 应用程序的网址
     */
    'APP_URL'               => 'http://www1.poco.cn/sms_service/',

    /**
     * 应用程序根目录
     */
    'ROOT_DIR'              => $root_dir,

    /**
     * 主程序类封装所在目录
     */
    'INCLUDE_DIR'           => "{$root_dir}/include",

    /**
     * 配置文件所在目录
     */
    'CONFIG_DIR'            => "{$root_dir}/config",

    /**
     * 网页控件扩展目录
     */
    'WEB_CONTROLS_EXTENDS_DIR'=> "{$root_dir}/webcontrols",
    
    /**
     * 定义缓存配置文件要使用的缓存服务
     *
     * 指定使用哪项服务，就需要在后面的 CONFIG_CACHE_SETTINGS 中进行相应的设置
     * 具体设置看缓存工具的扩展类，这里默认使用POCO_Cache，不需要后面的设置。
     */
    'CONFIG_CACHE_BACKEND'  => 'POCO_Cache',

    /**
     * 指示是否缓存配置文件的内容
     */
    'CONFIG_CACHED'         => false,

    /**
     * 缓存设置
     */
    'CONFIG_CACHE_SETTINGS' => array(
    ),
	
    /**
     * 自定义数组
     */
    'CUSTOM_ARRAY_SETTING' => array(	
	)
);
