<?php

/**
 * 公共协议文件 ( 入口 )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-5
 * @description 若非协议处理请求(include), 则 数据变量 必须为 $post_data
 */
// 定义 是否引用协议
if (!defined('YUE_INVOCATION_PROTOCOL')) {
    define('YUE_INVOCATION_PROTOCOL', TRUE); // 引入协议
}
// 定义是否 验证access_token
if (!defined('YUE_INPUT_CHECK_TOKEN')) {
    define('YUE_INPUT_CHECK_TOKEN', TRUE);
}
// 是否需要引入全局DB类
if (!isset($DB) || empty($DB)) {
    global $DB;
}
// 引入框架
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// 引入协议
$yue_protocol_path = str_replace('\\', '/', dirname(dirname(__FILE__))) . '/protocol/';
require($yue_protocol_path . 'yue_protocol.inc.php');
// 获取客户端的数据
$cp = new yue_protocol_system();
// 判断是否 使用协议
if (YUE_INVOCATION_PROTOCOL === FALSE) {
    // 使用 include 引入 ( for web )
    if (empty($post_data)) {
        exit('POST data is empty!');
    }
    $client_data = $cp->get_input_process($post_data, YUE_INPUT_CHECK_TOKEN, FALSE);
} else {
    // 正常请求 ( for APP )
    $client_data = $cp->get_input(array('be_check_token' => YUE_INPUT_CHECK_TOKEN));
}
require('protocol_methods.func.php');  // 引入公共方法
// 引用yueyue底层公共方法
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
// 统一处理 用户ID 2015-9-23
//if(!is_numeric($client_data['data']['param']['user_id'])){
//    $client_data['data']['param']['user_id'] = '0';
//}
$user_id = $client_data['data']['param']['user_id'];
$version = $client_data['data']['version'];  // 版本号

// 添加统计 2015-11-24
$tongji_type = filter_input(INPUT_SERVER, 'REQUEST_URI');
$tongji_query = 'query=' . serialize($client_data['data']);
yueyuetj_touch_log($tongji_type, $tongji_query);