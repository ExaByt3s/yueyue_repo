<?php

//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/index.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************

$ret_data["morecase_url"] = './service_list.php?seller_user_id='.$seller_user_id.'&tag=goods';

$tpl->assign('seller_user_id',$seller_user_id);
$tpl->assign('service_status',$service_status);
$tpl->assign('goods_type',$goods_type);
$tpl->assign('stars_width',$stars_width);
$tpl->assign('list_img_data',$list_img_data);
$tpl->assign('name',$ret_data["name"]);
$tpl->assign($ret_data);

?>