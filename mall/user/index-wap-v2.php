<?php
$task_templates_root = TASK_TEMPLATES_ROOT;

// �°���ҳ�ı���
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}
//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index-v2.tpl.htm');

// ͷ��css���
include_once($task_templates_root.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once($task_templates_root.$pc_wap. '/webcontrol/footer.php');
// ����tips ����
include_once($task_templates_root.$pc_wap. '/webcontrol/global_tips.php');


$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();
$wap_global_tips =  _get_wbc_tips();

$tpl->assign('wap_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
$tpl->assign('wap_global_tips', $wap_global_tips);

//****************** wap�� ͷ��ͨ�� end  ******************


$tpl->assign('ret_index_v2', $ret_index_v2['data']);


?>