<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/input.tpl.html');


// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

$limit_num = intval($_INPUT['limit_num']);
$tpl->assign('limit_num',$limit_num);

$input_title = trim($_INPUT['input_title']);
$input_title = urldecode($input_title);
$input_title = mb_convert_encoding($input_title,'gbk','utf-8');

$type = trim($_INPUT['type']);

$input_content = trim($_INPUT['input_content']);
$input_content = urldecode($input_content);
$input_content = mb_convert_encoding($input_content,'gbk','utf-8');

$tpl->assign('input_content',$input_content);
$tpl->assign('input_title',$input_title);
$tpl->assign('type',$type);

$tpl->output();
?>