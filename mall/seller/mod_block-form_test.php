<?php

/**
 * ��Ʒ�������ҳ����ӻ��߱༭��
 *
 * 2015-6-17
 *
 * author  ����
 *
 */
include_once 'common.inc.php';


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$mall_obj = POCO::singleton('pai_mall_seller_class');


$goods_id = (int)$_INPUT['goods_id']; //���ܲ���

// ����У��


$pc_wap = 'pc/';


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'mod-block-form-test.htm');




// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ������
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

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