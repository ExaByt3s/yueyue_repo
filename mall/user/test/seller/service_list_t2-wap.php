<?php

//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'/seller/service_list_t2.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('web_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************


$page_params = $_GET;

$page_params = mall_output_format_data($page_params);
$tpl->assign('page_params',$page_params);
$tpl->assign('title',$main_title);
//$tpl->assign('title','�����б�');




?>