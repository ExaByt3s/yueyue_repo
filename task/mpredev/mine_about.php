<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./mine_head.php');
$tpl = $my_app_pai->getView('mine_about.tpl.htm');

// 公共样式和js引入
$m_task_top = $my_app_pai->webControl('m_task_top', array(), true);
$tpl->assign('m_task_top', $m_task_top);

// // 头部引入
$m_global_top = $my_app_pai->webControl('m_global_top', array(), true);
$tpl->assign('m_global_top', $m_global_top);

// // 底部引入
$m_global_bot = $my_app_pai->webControl('m_global_bot', array(), true);
$tpl->assign('m_global_bot', $m_global_bot);

// 用户信息
$user_icon = get_user_icon($yue_login_id);
$user_nickname = get_user_nickname_by_user_id($yue_login_id);

$tpl->assign('user_icon', $user_icon);
$tpl->assign('user_nickname', $user_nickname);


$task_profile_obj = POCO::singleton('pai_task_profile_class');
$profile_info = $task_profile_obj->get_profile_info($yue_login_id,2);
$tpl->assign('profile_info', $profile_info);

$tpl->assign('time', time());  //随机数


$tpl->output();
 ?>