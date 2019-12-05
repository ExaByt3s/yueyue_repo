<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
 
$tpl = $my_app_pai->getView('change_pw.tpl.htm');

// 公共样式和js引入
$m_task_top = $my_app_pai->webControl('m_task_top', array(), true);
$tpl->assign('m_task_top', $m_task_top);

// // 头部引入
$m_global_top = $my_app_pai->webControl('m_global_top', array(), true);
$tpl->assign('m_global_top', $m_global_top);

// // 底部引入
$m_global_bot = $my_app_pai->webControl('m_global_bot', array(), true);
$tpl->assign('m_global_bot', $m_global_bot);

$tpl->assign('time', time());  //随机数


$tpl->output();
 ?>