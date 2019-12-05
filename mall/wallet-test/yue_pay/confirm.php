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


/** 
 * 页面接收参数
 */
$seller_user_id = intval($_INPUT['seller_user_id']) ;



//****************** wap版 头部通用 start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/confirm.tpl.htm');

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

$seller_user_name = get_seller_nickname_by_user_id($seller_user_id);

$seller_user_img  =  get_seller_user_icon($seller_user_id, $size = 86, $force_reflesh=false) ;
 
// 成功支付的回链
$redirect_url = G_MALL_PROJECT_WALLET_ROOT.'/yue_pay/success.php' ;


$tpl->assign('seller_user_img', $seller_user_img);
$tpl->assign('seller_user_name', $seller_user_name);
$tpl->assign('seller_user_id', $seller_user_id);

$tpl->assign('redirect_url', $redirect_url);


//user路径配置
$tpl->assign('G_MALL_PROJECT_USER_ROOT', G_MALL_PROJECT_USER_ROOT);


$tpl->output();
?>