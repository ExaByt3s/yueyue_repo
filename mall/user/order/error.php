<?php
include_once 'config.php';
$pc_wap = 'wap/';

$order_sn = trim($_INPUT['order_sn']);

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'order/error.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$tpl->assign('title','订单异常');
$tpl->assign('content','很抱歉，该订单号不存在');

$tpl->output();
?>