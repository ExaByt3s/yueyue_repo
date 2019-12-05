<?php
/**
 * 错误日志
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-11-10
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: handler error!');
$cache_key = 'MASTER_PROTOCOL_HANDLER_ERROR_LIST';
$error_list = yue_protocol_cache::get_cache($cache_key);
if (!empty($error_list) && $error_list != '[]') {
    return array('list' => $error_list,);
}
$file = yue_protocol_log::get_error_handler_file();  // 获取错误文件路径
$log_operate = new yue_log_operate();
$error_result = $log_operate->get_log_section($file, 25, -1, PHP_EOL);  // 获取内容
if (empty($error_result)) {
    return array('list' => array(), 'message' => '没有错误日志');
}
foreach ($error_result['list'] as $error) {
    $error_arr = explode('^$^', $error);
    $error_list[] = array(
        'time' => $error_arr[0],
        'code' => $error_arr[1],
        'url' => $error_arr[2],
        'message' => $error_arr[3],
        'params' => $error_arr[4],
        'agent' => pm_client_agent($error_arr[5]),
    );
}
rsort($error_list);  // 结果倒序
yue_protocol_cache::set_cache($cache_key, $error_list, array('life_time' => 30 * 60));  // 缓存30分

return array('list' => $error_list, 'message' => '获取缓存数据失败');