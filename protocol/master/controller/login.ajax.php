<?php

/**
 * 登陆 操作
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-21
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: login error!');
$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');
if (empty($username) || empty($password)) {
    return pm_json_return(array(), '用户名或密码为空', 0);
}
if (strlen($username) != 11 || strlen($password) < 6) {
    return pm_json_return(array(), '用户名或密码不正确', 0);
}
$params = array(
    'phone' => $username,
    'pwd' => $password,
);
$login_res = pm_yue_curl('ssl/mall/login.php', $params);
if (empty($login_res)) {
    return pm_json_return(array(), '登陆失败', 0);
}
$login_arr = json_decode($login_res, true);
$user_info = $login_arr['data'];
if ($user_info['code'] != 1) {
    return pm_json_return(array(), '用户名或密码错误', 0);
}
$user_id = $user_info['user_id'];  // 用户ID
// 判断用户是否有权限登陆
if (!in_array($user_id, pm_conf('LOGIN_ACCESS')) && !pm_is_admin($user_id)) {
    return pm_json_return(array(), '无权限登陆系统,请联系@willike', 0);
}
$nickname = $user_info['nickname']; // 昵称
$avatar = $user_info['user_icon'];  // 头像
pm_set_login_status($user_id, $nickname);  // 设置登陆信息
$redirect = pm_is_admin() ? '/protocol/master/?c=expo' : '/protocol/master/?c=api';
$return_res = array(
    'user_id' => $user_id,
    'user_name' => $nickname,
    'avatar' => $avatar,
    'redirect' => $redirect,
);
return pm_json_return($return_res, '登陆成功', 1);