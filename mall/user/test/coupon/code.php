<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 17 July, 2015
 * @package default
 */

/**
 * Define �Ż�ȯ����
 */
include_once 'config.php';

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'coupon/code.tpl.html');

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$redirect_url = trim($_INPUT['redirect_url']);
$tpl->assign('redirect_url', $redirect_url);

$tpl->output();	
?>