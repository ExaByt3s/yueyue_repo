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
$seller_user_id = intval($_INPUT['seller_user_id']) ;



//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/confirm.tpl.htm');

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

$seller_user_name = get_seller_nickname_by_user_id($seller_user_id);

$seller_user_img  =  get_seller_user_icon($seller_user_id, $size = 86, $force_reflesh=false) ;
 
// �ɹ�֧���Ļ���
$redirect_url = G_MALL_PROJECT_WALLET_ROOT.'/yue_pay/success.php' ;


$tpl->assign('seller_user_img', $seller_user_img);
$tpl->assign('seller_user_name', $seller_user_name);
$tpl->assign('seller_user_id', $seller_user_id);

$tpl->assign('redirect_url', $redirect_url);


//user·������
$tpl->assign('G_MALL_PROJECT_USER_ROOT', G_MALL_PROJECT_USER_ROOT);


$tpl->output();
?>