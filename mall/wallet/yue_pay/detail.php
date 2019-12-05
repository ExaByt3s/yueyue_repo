<?php
include_once 'config.php';


define('MALL_NOT_REDIRECT_LOGIN',1);

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}


// ========================= 初始化接口 start =======================

$order_sn = intval($_INPUT['order_sn']) ;



//****************** wap版 头部通用 start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/detail.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************

//约付
$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');



/**
 * 获取完整信息
 * @param string $order_sn 订单号
 * @param int $login_user_id
 * @return array
 */
$order_full_info = $order_payment_obj->get_order_full_info($order_sn, $yue_login_id);


if ($order_full_info['seller_user_id'] == $yue_login_id) 
{
	$img_url = $order_full_info['buyer_icon'];
	$name =  $order_full_info['buyer_name'];
	$id = $order_full_info['buyer_user_id'];
	$title = '收款明细' ;
	// $txt = "买家留言";
	$txt = "买家留言";

	$role = "buyer" ;

} 
elseif ($order_full_info['buyer_user_id'] == $yue_login_id)
{
	$img_url = $order_full_info['seller_icon'];
	$name =  $order_full_info['seller_name'];
	$id = $order_full_info['seller_user_id'];
	$title = '付款明细' ;
	// $txt = "卖家留言";
	$txt = "买家留言";

	$role = "seller" ;
}

else
{
	echo "非法操作！";
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