<?php
include_once 'config.php';


define('MALL_NOT_REDIRECT_LOGIN',1);

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

// ========================= ��ʼ���ӿ� start =======================

$order_sn = intval($_INPUT['order_sn']) ;



//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/pay_list.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************

//Լ��
$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');

/**
 * ����̼����ѱ�־
 * @param int $seller_user_id
 * @param string $order_sn �ձ�ʾ���ȫ��
 * @return bool
 */
if ($order_sn) 
{
	$rst = $order_payment_obj->clear_seller_remind($yue_login_id, $order_sn);
} 
else 
{
	$rst = $order_payment_obj->clear_seller_remind($yue_login_id, '');
}




$tpl->output();
?>