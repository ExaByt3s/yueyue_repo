<?php
include_once 'config.php';

// ========================= ��ʼ���ӿ� start =======================

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if($check_arr['switch'] == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}


$topic_id =  intval($_INPUT['topic_id']);
//****************** wap�� ͷ��ͨ�� start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'trade/submit.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************


$tpl->assign('topic_id', $topic_id);
$tpl->assign('limit_num',140);
// ========================= ����ģ�����  =======================
$tpl->output();

?>