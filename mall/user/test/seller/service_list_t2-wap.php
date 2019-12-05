<?php

//****************** wap版 头部通用 start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'/seller/service_list_t2.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('web_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************


$page_params = $_GET;

$page_params = mall_output_format_data($page_params);
$tpl->assign('page_params',$page_params);
$tpl->assign('title',$main_title);
//$tpl->assign('title','服务列表');




?>