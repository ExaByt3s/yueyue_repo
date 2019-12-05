<?php

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/seller_list.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$img_size = trim($_INPUT['img_size'])?trim($_INPUT['img_size']) : 'big';

// 输出页面参数
$page_params = mall_output_format_data(array
	(
		'return_query'=>$return_query,
		'img_size'=>$img_size
	));



$tpl->assign('page_params',$page_params);
$tpl->assign('img_size',$img_size);

?>