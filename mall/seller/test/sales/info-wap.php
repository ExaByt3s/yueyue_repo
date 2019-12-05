<?php

//****************** wap版 头部通用 start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'sales/info.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

// 输出页面参数
$page_params = mall_output_format_data(array
(
	'topic_id'=>$topic_id
));



$tpl->assign('page_params',$page_params);
$tpl->assign('state',$state);
$tpl->assign('topic_id',$topic_id);
$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************








?>