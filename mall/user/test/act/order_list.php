<?php
/**
 * ����� ҳ��
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 16 July, 2015
 * @package default
 */

include_once 'config.php';
// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);
// �˺��л�ʱ
if ($check_arr['switch'] == 1) {
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    header("Location:{$url}");
    die();
}
/**
 * $type -- ��ȡ���ݵ�����
 * $tpl = $my_app_pai -- ���ؽ�ģ��
 */
$type = trim($_INPUT['type']);
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT . $pc_wap . 'act/order_list.tpl.html');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT . $pc_wap . '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT . $pc_wap . '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

if (empty($type)) {
    $type = 'unpaid';
}

$page_params = array(
    'type' => $type
);

$page_params = mall_output_format_data($page_params);

$tpl->assign('page_params', $page_params);
$tpl->assign('type', $type);

$tpl->assign('user_id', $yue_login_id);
$tpl->assign('user_icon', get_user_icon($yue_login_id, 165));
$tpl->assign('nick_name', get_user_nickname_by_user_id($yue_login_id));

$tpl->output();