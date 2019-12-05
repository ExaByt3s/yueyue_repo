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
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/pay_list.tpl.htm');

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
 * 清除商家提醒标志
 * @param int $seller_user_id
 * @param string $order_sn 空表示清除全部
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