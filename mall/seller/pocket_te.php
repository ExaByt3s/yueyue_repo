<?php

/**
 * ��Ʒ�������ҳ����ӻ��߱༭��
 *
 * 2015-6-29
 *
 * author  nolest
 *
 */

include_once 'common.inc.php';

$user_id = $yue_login_id;

$pc_wap = 'wap/';

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'pocket.tpl.htm');

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

$obj = POCO::singleton('pai_user_class');

$ret = $obj->get_user_info_by_user_id($yue_login_id);
$tpl->assign('info', $ret);

$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$bind_status = $pai_bind_account_obj->get_bind_status($yue_login_id,'alipay_account');
				//-1 δ�� 0 ����� 1����� 2��˲�ͨ��
				switch ($bind_status['status']) {
					case '-1':
						$status_resault['code'] = -1;
						$status_resault['msg']  = 'δ��';
						break;
					case '0':
						$status_resault['code'] = -2;
						$status_resault['msg']  = '�����';
						break;
					case '1':
						$status_resault['code'] = 1;
						$status_resault['msg']  = '�Ѱ�';
						break;
					case '2':
						$status_resault['code'] = -3;
						$status_resault['msg']  = '��˲�ͨ��';
						break;
					default:
						break;
				}
$tpl->assign('status_resault', $status_resault);

dump($tpl);

$tpl->assign('num',123);
$tpl->output();

?>