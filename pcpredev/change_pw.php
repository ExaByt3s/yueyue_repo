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
# �������������������� ���� ����get_hash�ķ������� 2015-11-17 ��ʯ��
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
# �������������������� ���� ����get_hash�ķ������� 2015-11-17 ��ʯ��


// ================== ����ģ�� ==================
$tpl = $my_app_pai->getView('change-password.tpl.htm');
# �������������������� ��ȡhashֵ 2015-11-17 ��ʯ��
$get_hash = POCO::singleton('validation_code_class')->get_hash();
$tpl->assign('token', $get_hash);
$device_arr = mall_get_user_agent_arr();
if($device_arr["is_pc"]==1)
{
    $is_pc = "pc";
}
else
{
    $is_pc = "else";
}
$tpl->assign('is_pc', $is_pc);
# �������������������� ��ȡhashֵ 2015-11-17 ��ʯ��

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