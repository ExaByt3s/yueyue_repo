<?php
include_once 'config.php';

//Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if ($check_arr['switch'] == 1) {
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    header("Location:{$url}");
    die();
}
//��ȡģ��
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT . $pc_wap . 'order/list.tpl.html');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT . $pc_wap . '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT . $pc_wap . '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

// ������
$mall_order_obj = POCO::singleton('pai_mall_order_class');

//��ƷƷ��ID
$type_id = intval($_INPUT['type_id']);
$type_id = !empty($type_id) ? $type_id : 0;
//����״̬��0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
$status = intval($_INPUT['status']);
$status = !empty($status) ? $status : 0;

$goods_type = ($type_id != 42) ? 'normal' : 'activity';

if($goods_type  == 'activity')
{
	/**
	 * ��ȡ��һ����������Ŀ����
	 * @param int $user_id ����û�ID
	 * @return array
	 */
	$red_dot = $mall_order_obj->get_order_number_by_activity_for_buyer($yue_login_id);
}
else
{
	/**
	 * ��ȡ��ҷ��񶩵�������Ŀ����
	 * @param int $user_id ����û�ID
	 * @return array
	 */
	$red_dot = $mall_order_obj->get_order_number_by_detail_for_buyer($yue_login_id);
}




$normal_link = './list.php?type_id=0';
$activity_link = './list.php?type_id=42';


if($goods_type == 'normal')
{
	$title = '���񶩵�';
}
else
{
	$title = '�����';
}

$tpl->assign('title', $title);
$tpl->assign('goods_type', $goods_type);
$tpl->assign('normal_link', $normal_link);
$tpl->assign('activity_link', $activity_link);
$tpl->assign('type_id', $type_id);
$tpl->assign('current_status', $status);
$tpl->assign('red_dot', $red_dot);
$tpl->assign('pay_url', '../pay/?order_sn=');
$tpl->assign('user_id', $yue_login_id);
$tpl->assign('status', $status);
$tpl->assign('user_icon', get_user_icon($yue_login_id, 165));
$tpl->assign('nick_name', get_user_nickname_by_user_id($yue_login_id));

//���ģ������
$tpl->output();