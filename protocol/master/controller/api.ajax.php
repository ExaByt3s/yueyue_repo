<?php
/**
 * API 操作
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-10-29
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: login error!');
$params = array();
$path = '';
foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        $path = $value;
        continue;
    }
    $params[$key] = $value;
}
if (empty($path)) {
    return pm_json_return(array(), '请选择调试API接口', 0);
}
if (empty($params)) {
    return pm_json_return(array(), '参数为空', 0);
}
$login_res = pm_get_login_status();
$user_id = pm_get_user_id();
// json 请求
if (isset($params['postman'])) {
    $postman = json_decode($params['postman'], TRUE);
    if (empty($postman)) {
        return pm_json_return(array(), 'json无法解析', 0);
    }
    $params = array_merge($params, $postman);
    unset($params['postman']);
}
if (!pm_is_admin()) {
    // 非管理员限制
    foreach (pm_conf('ONESELF_API') as $api) {
        if (strpos($path, $api) !== FALSE) {
            if ($params['user_id'] != $user_id) {
                return pm_json_return(array(), '该接口user_id只能是当前登录用户ID', 0);
            }
        }
    }
}
// 检查版本的正确性
$version = $params['version'];
if (!preg_match('/^\d+\.\d+\.\d+/', $version)) {
    return pm_json_return(array(), '版本号不合法', 0);
}
if (empty($params['user_id'])) {// 赋值user_id
    $params['user_id'] = $user_id;
}
$api_res = pm_yue_curl($path, $params);
if (empty($api_res)) {
    return pm_json_return(array(), '数据获取失败', 0);
}
return pm_json_return(array('contents' => $api_res), '获取成功!', 1);
