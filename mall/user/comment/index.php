<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 13 July, 2015
 * @package default
 */

/**
 * Define 评价页面
 */

include_once 'config.php';

// 权限检查
mall_check_user_permissions($yue_login_id);
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'comment/index.tpl.htm');

$order_sn = trim($_INPUT['order_sn']);
$table_id = intval($_INPUT['table_id']);

$redirect_url = trim($_INPUT['redirect_url']);


// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

if(!empty($order_sn))
{
	$comment_info = array(
		0 => array(
			'text' => '总体评价：',
			'role' => 'overall_score'
		),
		1 => array(
			'text' => '描述相符：',
			'role' => 'match_score'
		),
		2 => array(
			'text' => '服务态度：',
			'role' => 'manner_score'
		),
		3 => array(
			'text' => '服务质量：',
			'role' => 'quality_score'
		)
	);
}
elseif (!empty($table_id))
{
	$comment_info = array(
		0 => array(
			'text' => '总体评价：',
			'role' => 'overall_score'
		),
		1 => array(
			'text' => '组织能力：',
			'role' => 'match_score'
		),
		2 => array(
			'text' => '模特水平：',
			'role' => 'quality_score'
		)
	);
}



$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
$tips = $coupon_give_obj->show_tips_for_comment_interface($order_sn);

$tpl->assign('redirect_url',$redirect_url);

$tpl->assign('comment_info',$comment_info);
$tpl->assign('order_sn',$order_sn);
$tpl->assign('table_id',$table_id);
$tpl->assign('tips',$tips);
$tpl->output();

?>