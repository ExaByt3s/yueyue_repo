<?php
/**
 * 默认配置文件
 */

return array
(
    // {{{ 运行环境相关
    
    /**
     * cache 系列函数使用的缓存目录
     * 应用程序必须设置该选项才能使用 cache 功能。
     */
    'runtime_cache_dir'         => null,

    /**
     * 默认使用的缓存服务
     */
    'runtime_cache_backend'     => 'POCO_Cache',

    /**
     * 网页控件目录
     */
    'web_controls_dir'          => G_POCO_APP_PATH . DS . 'webcontrols',

    // }}}
    
    // {{{ 日志和错误处理

    /**
     * 指示是否允许记录日志
     */
    'log_enabled' => true,

    /**
     * 指示记录哪些优先级的日志（不符合条件的会直接过滤）
     */
    'log_priorities' => 'EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG',

    /**
     * 日志缓存块大小（单位KB）
     *
     * 更小的缓存块可以节约内存，但写入日志的次数更频繁，性能更低。
     */
    'log_cache_chunk_size' => 64,  // 64KB

    /**
     * 保存日志文件的目录
     */
    'log_writer_dir' => null,

    /**
     * 日志文件的文件名
     */
    'log_writer_filename' => 'access.log',
    
    /**
     * 指示是否显示错误信息（有一定安全风险）
     *
     * 在生产环境建议关闭此功能。
     */
    'error_display'             => true,

    /**
     * 指示是否显示友好的错误信息（有安全风险）
     *
     * 在生产环境必须关闭此功能。
     */
    'error_display_friendly'    => true,

    /**
     * 指示是否在错误信息中显示出错位置的源代码（有安全风险）
     *
     * 在生产环境必须关闭此功能。
     */
    'error_display_source'      => true,
    
    // }}}
);
