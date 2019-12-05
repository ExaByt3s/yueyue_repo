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
// $id = intval($_INPUT['topic_id']) ;



//****************** wap版 头部通用 start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'yue_pay/show_qrcode.tpl.htm');

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
 * 获取商家直接付款的二维码
 * @param string $url 直接付款的网址，http开头
 * @param int $seller_user_id 收款人用户ID
 * @return array array('result'=>0, 'message'=>'', 'qr_code_url'=>'')
 */

$url = G_MALL_PROJECT_WALLET_ROOT.'/yue_pay/confirm.php' ;
$ret = $order_payment_obj->get_seller_qr_code_info($url, $yue_login_id);



/**
 * 获取商家提醒数量
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