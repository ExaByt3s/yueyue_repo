<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 13 July, 2015
 * @package default
 */

/**
 * Define ����ҳ��
 */

include_once 'config.php';

// Ȩ�޼��
mall_check_user_permissions($yue_login_id);
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'comment/index.tpl.htm');

$order_sn = trim($_INPUT['order_sn']);
$table_id = intval($_INPUT['table_id']);

$redirect_url = trim($_INPUT['redirect_url']);


// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

if(!empty($order_sn))
{
	$comment_info = array(
		0 => array(
			'text' => '�������ۣ�',
			'role' => 'overall_score'
		),
		1 => array(
			'text' => '���������',
			'role' => 'match_score'
		),
		2 => array(
			'text' => '����̬�ȣ�',
			'role' => 'manner_score'
		),
		3 => array(
			'text' => '����������',
			'role' => 'quality_score'
		)
	);
}
elseif (!empty($table_id))
{
	$comment_info = array(
		0 => array(
			'text' => '�������ۣ�',
			'role' => 'overall_score'
		),
		1 => array(
			'text' => '��֯������',
			'role' => 'match_score'
		),
		2 => array(
			'text' => 'ģ��ˮƽ��',
			'role' => 'quality_score'
		)
	);
}



$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
$tips = $coupon_give_obj->show_tips_for_comment_interface($order_sn);

$tpl->assign('redirect_url',$redirect_url);

$tpl->assign('comment_info',$comment_info);
$tpl->assign('order_sn',$order_sn);
$tpl->assign('table_id',$table_id);
$tpl->assign('tips',$tips);
$tpl->output();

?>