<?php
/**
 * 应用程序运行时的日志记录和错误处理配置
 */

return array(

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
     * 保存日志文件的目录
     */
    'log_writer_dir' => dirname(dirname(__FILE__)) . '/log',

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
