<?php

//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods/service_list.tpl.html');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tag = trim($_INPUT['tag']);

$tpl->assign('wap_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************


// �����ģ�����ת��
$return_query = mb_convert_encoding($return_query, 'gbk', 'utf8');

$_GET['title'] = $main_title;
$page_params = mall_output_format_data($_GET);


$tpl->assign('page_params',$page_params);
$tpl->assign('title',$main_title);
$tpl->assign('tag',$tag);

?>