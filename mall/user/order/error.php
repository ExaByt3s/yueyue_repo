<?php
include_once 'config.php';
$pc_wap = 'wap/';

$order_sn = trim($_INPUT['order_sn']);

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'order/error.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$tpl->assign('title','�����쳣');
$tpl->assign('content','�ܱ�Ǹ���ö����Ų�����');

$tpl->output();
?>