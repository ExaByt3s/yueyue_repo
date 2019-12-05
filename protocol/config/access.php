<?php

/**
 * token 配置文件
 *   ( 日志根据 group_id 分类 写入文件 )
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-31
 */
return array(
    // 约拍APP（iPhone版）
    'poco_yuepai_iphone' => array(
        'app_id' => 3,
        'app_name' => 'poco_yuepai_iphone',
        'token_expire_time' => 30, // 天
        'group_id' => 2
    ),
    // 约拍APP（Android版）
    'poco_yuepai_android' => array(
        'app_id' => 4,
        'app_name' => 'poco_yuepai_android',
        'token_expire_time' => 30,
        'group_id' => 2
    ),
    // 约拍APP（Web版）
    'poco_yuepai_web' => array(
        'app_id' => 5,
        'app_name' => 'poco_yuepai_web',
        'token_expire_time' => 30,
        'group_id' => 2
    ),
    // 约拍APP（Api调用）
    'poco_yuepai_api' => array(
        'app_id' => 6,
        'app_name' => 'poco_yuepai_api',
        'token_expire_time' => 30,
        'group_id' => 2
    ),
);
