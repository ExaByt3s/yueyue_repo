#约约通信协议 1.0 Beta

----

## 协议说明

    该协议用于约约客户端与服务器端之间的通信。
    协议负责将对数据进行签名校验，HTML注入过滤，字符编码转译等处理。

## 构成模块

 * 数据接收：处理客户端的数据请求，数据校验，签名校验等；
 * 用户授权：处理用户access_token注册，授权验证，刷新验证等； 
 * 请求响应：负责服务端数据组装，编码处理（加密处理），抛出数据等；

## 引用路径

    /protocol/yue_protocol.inc.php

## 方法实现 (类实例化)

    new yue_protocol_system();

## 常用方法

> $cp = new yue\_protocol\_system();

 * 获取客户端数据(http)：`$cp->get_input(array $options)`;
 * 获取输入数据(include)：`$cp->get_input_process($input, $token_check, $b_response)`;
 * 抛出(或返回)服务端数据：`$cp->output($options, $is_conv)`;
 * 获取TOKEN数据：`$cp->get_access_info($user_id, $app_name, $b_auto_create, $b_use_cache)`;
 * 刷新TOKEN数据：`$cp->refresh_access_info($user_id, $app_name, $refresh_token, $access_info)`;
 * 验证TOKEN数据：`$cp->is_access_expire($user_id, $app_name, $access_token, $access_info)`;
 * 获取app name：`$cp->get_app_name()`;

## 附加功能
    
    /protocol/master/index.php
    协议管理后台
