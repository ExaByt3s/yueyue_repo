<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 29 July, 2015
 * @package default
 */

/**
 * Define 通知提示页面
 */

include_once 'config.php';

$pc_wap = 'wap/';

$type = trim($_INPUT['type']);

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'notice/notice.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

switch($type)
{
	case 'order':
	$message = '提交失败,请勿重复提交订单';
	break;
	case 'pay':
	$message = '支付失败,请勿重复提交支付信息';
	break;
}


$tpl->assign('message',$message);
$tpl->assign('return_url',G_MALL_PROJECT_USER_ROOT.'/index.php');

$tpl->output();	
?>