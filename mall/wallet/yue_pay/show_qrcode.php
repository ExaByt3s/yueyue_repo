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


/** 
 * ҳ����ղ���
 */
// $id = intval($_INPUT['topic_id']) ;



//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/show_qrcode.tpl.htm');

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
 * ��ȡ�̼�ֱ�Ӹ���Ķ�ά��
 * @param string $url ֱ�Ӹ������ַ��http��ͷ
 * @param int $seller_user_id �տ����û�ID
 * @return array array('result'=>0, 'message'=>'', 'qr_code_url'=>'')
 */

$url = G_MALL_PROJECT_WALLET_ROOT.'/yue_pay/confirm.php' ;
$ret = $order_payment_obj->get_seller_qr_code_info($url, $yue_login_id);



/**
 * ��ȡ�̼���������
 * @param int $seller_user_id
 * @return int
 */

$remind_count = $order_payment_obj->sum_seller_remind($yue_login_id);




$tpl->assign('qr_code_url', $ret['qr_code_url']);
$tpl->assign('remind_count', $remind_count);



if ($_INPUT['print']) 
{
    print_r($ret);
}


$tpl->output();
?>