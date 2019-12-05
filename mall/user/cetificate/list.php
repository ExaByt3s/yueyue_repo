<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'cetificate/list.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$level_list = $user_level_obj->level_list($yue_login_id);

$level_detail = $user_level_obj->level_detail($yue_login_id);

$output_arr['list'] = $level_list;

$output_arr['data'] = $level_detail;

//$level_detail = mall_output_format_data($level_detail);

$tpl->assign('message','在进行v3认证前，约约君将引导你完成v2认证哦，谢谢');

$tpl->assign('list',$level_list);
$tpl->assign('level_detail',$level_detail);

if($_INPUT['print'] == 1)
{
	print_r($level_detail);
	die();
}

$tpl->output();
?>