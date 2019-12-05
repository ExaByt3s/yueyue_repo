<?php
include_once 'config.php';
$pc_wap = 'wap/';

$task_obj = POCO::singleton('pai_mall_order_class');

$order_sn = trim($_INPUT['order_sn']);

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'pay/success.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

// 根据订单查询用户信息

$order_info = $task_obj->get_order_info($order_sn);

$order_type = $order_info['order_type'];

$seller_user_id = $order_info['seller_user_id'];

$sendername = get_user_nickname_by_user_id($yue_login_id);
$receivername = get_user_nickname_by_user_id($seller_user_id);

$sendericon = get_user_icon($yue_login_id,165);
$receivericon = get_user_icon($seller_user_id,165);

$ret_json = array(
	'senderid' => $yue_login_id,
	'receiverid' => $seller_user_id,
	'sendername' => $sendername,
	'receivername' => $receivername,
	'sendericon' => $sendericon,
	'receivericon' => $receivericon
);

/**
 * 判断客户端
 */
if(MALL_UA_IS_YUEYUE == '1')
{
	$is_yueyue_app = true;
}
elseif(MALL_UA_IS_WEIXIN == '1')
{
	$is_weixin = true;

	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    $wx_user_base_info = $weixin_pub_obj->get_user_weixin_base_info_by_user_id($yue_login_id);
    $is_subscribe = $wx_user_base_info['is_subscribe'];

    // 关注了，就不显示公众号二维码
    if($is_subscribe)
    {
    	$is_weixin = false;
    }
}

$ret_json = mall_output_format_data($ret_json);
$ret_json = urlencode($ret_json);

// 马上支付，跳转到钱包的订单详情
if($order_type == 'payment')
{
	$order_detail_url = 'http://yp.yueus.com/mall/wallet/yue_pay/detail.php?order_sn='.$order_sn;
}
else
{
	$order_detail_url = '../order/detail.php?order_sn='.$order_sn;
}


$tpl->assign('order_detail_url',$order_detail_url);
$tpl->assign('go_to_other',G_MALL_PROJECT_USER_ROOT.'/index.php');
$tpl->assign('chat_json',$ret_json);
$tpl->assign('is_yueyue_app',$is_yueyue_app);
$tpl->assign('is_weixin',$is_weixin);
$tpl->assign('order_sn',$order_sn);
$tpl->assign('order_type',$order_type);

$tpl->output();
?>