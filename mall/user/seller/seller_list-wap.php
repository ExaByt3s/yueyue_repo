<?php

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/seller_list.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$img_size = trim($_INPUT['img_size'])?trim($_INPUT['img_size']) : 'big';

// ���ҳ�����
$page_params = mall_output_format_data(array
	(
		'return_query'=>$return_query,
		'img_size'=>$img_size
	));



$tpl->assign('page_params',$page_params);
$tpl->assign('img_size',$img_size);

?>