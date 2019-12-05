<?php

/**
 * 协议管理后台入口 ( 唯一入口 )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-22
 */
// 定义后台路径
define('PROTOCOL_MASTER_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

require_once(PROTOCOL_MASTER_ROOT . 'master_func.php');  // 引入公共方法

$action = filter_input(INPUT_GET, 'c');  // 操作
if (!pm_is_login() && !in_array($action, pm_conf('NO_LOGIN_REQUIRED_ACTION'))) {
    // 未登录,跳转到 登陆页面
    return pm_redirect('/protocol/master/?c=login');
}

pm_write_visit_log();  // 写访问日志

if (empty($action)) {
    $action = 'index';
} else if ($action == 'logout') { // 退出
    pm_drop_login_status();
    return pm_redirect('http://www.yueus.com');
}
if (pm_get_login_status()) {
    if (!pm_is_login_access() && !pm_is_admin()) {
        // 判断用户是否有权限登陆
        if (pm_is_post()) {
            return pm_json_return(array('user_id' => pm_get_user_id()), '权限不够!', 0);
        }
        return pm_redirect('/protocol/master/?c=login');
    }
}

if (in_array($action, pm_conf('ADMIN_ACCESS_ACTION'))) { // 需要管理员权限
    if (!pm_is_admin()) { // 非管理员
        if (pm_is_post()) {
            return pm_json_return(array(), '权限不够!', 0);
        }
        return pm_redirect('/protocol/master/?c=index');
    }
}
require_once(PROTOCOL_MASTER_ROOT . 'master_init.php');  // 引入

if (pm_check_single_login() == false && !in_array($action, pm_conf('NO_LOGIN_REQUIRED_ACTION'))) {
    // 被踢出
    pm_drop_login_status();  // 退出登录
    if (pm_is_post()) {
        return pm_json_return(array('user_id' => pm_get_user_id()), '该账号已在其他地方登陆!', 0);
    }
    // 页面显示
    $data = array(
        'info' => '<span class="text text-danger">该账号</span>已经在其他地方登陆！',
        'button_left' => '<a href="/protocol/master/?c=login" class="btn btn-primary btn-lg">
	                <span class="glyphicon glyphicon-repeat"></span>&nbsp;重新登陆</a>',
        'button_right' => '',
        'user' => pm_get_login_status(),
    );
    return pm_render('info', $data, TRUE);
}

if (!pm_is_post()) {
    $data = array();
    if (!empty($action)) {
        $action_file = PROTOCOL_MASTER_ROOT . '/controller/' . $action . '.action.php';
        if (file_exists($action_file)) {
            $data = include($action_file);
        }
        $data = is_array($data) ? $data : array();
    }
    $data['user'] = pm_get_login_status();  // 用户基本信息
    $head = in_array($action, array('login', 'error', 'detect')) ? FALSE : TRUE;  // 是否引入头部
//    var_dump($data);exit;
    return pm_render($action, $data, $head);
}
if (!empty($action)) {
    $ajax_file = PROTOCOL_MASTER_ROOT . '/controller/' . $action . '.ajax.php';  //
    if (file_exists($ajax_file)) {
        return include($ajax_file);
    }
}
return pm_json_return(array('action' => $action), '请求错误!', 0);
