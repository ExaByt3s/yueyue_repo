<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'order/auto.tpl.html');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap.'/webcontrol/head.php');

// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

/*
if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = 'login,error';
	$output_arr['data'] = array();
	exit();
}
*/

$tpl->output();
?>
