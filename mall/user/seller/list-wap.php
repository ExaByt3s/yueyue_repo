<?php

//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/list.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************

$return_query = trim($_INPUT['return_query']);
$page_params = mall_output_format_data(array('return_query'=>$return_query));

if($_INPUT['print'] == 1)
{
    print_r($_INPUT['title']);
    die();
}

//$tpl->assign('title','�����б�');

$tpl->assign('page_params',$page_params);
$tpl->assign('title',mb_convert_encoding(urldecode($_INPUT['title']), 'gbk', 'utf8'));

?>