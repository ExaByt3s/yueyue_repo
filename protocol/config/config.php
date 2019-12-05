<?php

/**
 * 协议配置
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-30
 */
return array(
    'PROTOCOL_VERSION' => 'v1', // 协议版本
    // 数据库
    'SERVER_ID' => 101, // 约约协议数据库服务器
    'DB_NAME' => 'm_service_db', // 协议数据库
    // 数据 安全
    'ENCRYPT_KEY' => 'poco_xxtea_v1234', // 加密密钥
    'DATA_LIMITED' => 0, // 数据有效期,不验证填 0 ( 单位: 秒 )
    'SIGN_CODE_LIMITED' => 0, // 签名有效期, 不验证填 0 ( 单位: 秒 ) 
    // 缓存
    'TOKEN_EXPIRE' => 15, // TOKEN有效期, 不缓存填 0 ( 单位: 天 ) 
    // 调试
    'OPEN_DEBUG' => TRUE, // 是否开启调试 ( - true 开启 ) 开启,则会输出程序处理时间
    'OPEN_VISIT_LOGGER' => TRUE, // 是否开启(访问)日志记录 ( -true 开启 )
    'OPEN_EVENT_LOGGER' => TRUE, // 是否开启(事件)日志记录 ( -true 开启 )
    'OPEN_RUNTIME_LOGGER' => TRUE, // 是否开启(耗时)日志记录 ( -true 开启 )
    'OPEN_SLOWQUERY_LOGGER' => TRUE, // 是否开启(慢查询)日志记录 ( -true 开启, 建议开启 )
    'OPEN_TOKEN_LOGGER' => TRUE, // 是否开启(TOKEN)日志记录 ( -true 开启, 建议开启 )
    'NON_TOKEN_AUTH' => 'poco_yuepai_api', // 无需TOKEN验证(支持数组和逗号分隔的字符串) 需OPEN_DEBUG 开启
    // 编码
    'CLIENT_DATA_CHARSET' => 'UTF-8', // 客户端(APP)数据编码格式
    'SERVICE_DATA_CHARSET' => 'GBK', // 服务端(数据库)数据编码格式
);
