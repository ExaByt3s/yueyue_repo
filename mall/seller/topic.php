<?php

/**
 * ��Ʒ�������ҳ����ӻ��߱༭��
 *
 * 2015-6-29
 *
 * author  nolest
 *
 */

define('MALL_SELLER_IS_NOT_LOGIN',1);

include_once 'common.inc.php';

$user_id = $yue_login_id;

$pc_wap = 'pc/';

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'topic.tpl.htm');

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

// ========================= ��ʼ���ӿ� start =======================
$topic_obj = POCO::singleton('pai_topic_class');

$user_agent_arr = mall_get_user_agent_arr();
/** 
 * ҳ����ղ���
 */
$id = intval($_INPUT['topic_id']) ;

$ret = $topic_obj->get_topic_info($id);

$tpl->assign('footer', $footer);
$tpl->assign('ret', $ret);


$tpl->output();

?>