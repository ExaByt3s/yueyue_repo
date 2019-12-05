<?php
include_once 'config.php';

//权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if ($check_arr['switch'] == 1) {
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    header("Location:{$url}");
    die();
}
//获取模板
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT . $pc_wap . 'order/list.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT . $pc_wap . '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT . $pc_wap . '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

// 订单类
$mall_order_obj = POCO::singleton('pai_mall_order_class');

//商品品类ID
$type_id = intval($_INPUT['type_id']);
$type_id = !empty($type_id) ? $type_id : 0;
//订单状态，0待支付，1待确认，2待签到，7已关闭，8已完成
$status = intval($_INPUT['status']);
$status = !empty($status) ? $status : 0;

$goods_type = ($type_id != 42) ? 'normal' : 'activity';

if($goods_type  == 'activity')
{
	/**
	 * 获取买家活动订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @return array
	 */
	$red_dot = $mall_order_obj->get_order_number_by_activity_for_buyer($yue_login_id);
}
else
{
	/**
	 * 获取买家服务订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @return array
	 */
	$red_dot = $mall_order_obj->get_order_number_by_detail_for_buyer($yue_login_id);
}




$normal_link = './list.php?type_id=0';
$activity_link = './list.php?type_id=42';


if($goods_type == 'normal')
{
	$title = '服务订单';
}
else
{
	$title = '活动订单';
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

//输出模板内容
$tpl->output();