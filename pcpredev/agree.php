<?php


/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);



// ͨ��
include_once($file_dir.'/./pc_common.inc.php');

// Ȩ���ļ�
include_once($file_dir.'/./pc_auth_common.inc.php');

// ͷ��css���
include_once($file_dir. '/./webcontrol/head.php');

// ������
include_once($file_dir. '/./webcontrol/global-top-bar.php');

// �ײ�
include_once($file_dir. '/./webcontrol/footer.php');

// ��������
include_once($file_dir. '/./webcontrol/down-app-area.php');


// ================== ����ģ�� ==================
$tpl = $my_app_pai->getView('agree.tpl.htm');



// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// ͷ��bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);

// �ײ�
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);




$tpl->output();
?>