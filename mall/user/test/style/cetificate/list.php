<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'cetificate/list.tpl.html');

$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$level_list = $user_level_obj->level_list($yue_login_id);

$level_detail = $user_level_obj->level_detail($yue_login_id);

$output_arr['list'] = $level_list;

$output_arr['data'] = $level_detail;

$tpl->assign('list',$level_list);
$tpl->assign('data',$level_detail);

$tpl->output();
?>