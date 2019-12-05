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
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/detail.tpl.htm');

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
 * ��ȡ������Ϣ
 * @param string $order_sn ������
 * @param int $login_user_id
 * @return array
 */
$order_full_info = $order_payment_obj->get_order_full_info($order_sn, $yue_login_id);


if ($order_full_info['seller_user_id'] == $yue_login_id) 
{
	$img_url = $order_full_info['buyer_icon'];
	$name =  $order_full_info['buyer_name'];
	$id = $order_full_info['buyer_user_id'];
	$title = '�տ���ϸ' ;
	// $txt = "�������";
	$txt = "�������";

	$role = "buyer" ;

} 
elseif ($order_full_info['buyer_user_id'] == $yue_login_id)
{
	$img_url = $order_full_info['seller_icon'];
	$name =  $order_full_info['seller_name'];
	$id = $order_full_info['seller_user_id'];
	$title = '������ϸ' ;
	// $txt = "��������";
	$txt = "�������";

	$role = "seller" ;
}

else
{
	echo "�Ƿ�������";
	die();
	exit();
}



if ($_INPUT['print']) 
{
	print_r($order_full_info);
}





$tpl->assign('img_url', $img_url);
$tpl->assign('name', $name);
$tpl->assign('id', $id);
$tpl->assign('ret', $order_full_info);
$tpl->assign('title', $title);
$tpl->assign('txt', $txt);
$tpl->assign('role', $role);


$redirect_url =  urlencode(G_MALL_PROJECT_WALLET_ROOT.'/yue_pay/detail.php?order_sn='.$order_sn) ;

$tpl->assign('comment_url', G_MALL_PROJECT_USER_ROOT.'/comment/index.php?order_sn='.$order_sn.'&redirect_url='.$redirect_url);


$tpl->output();
?>