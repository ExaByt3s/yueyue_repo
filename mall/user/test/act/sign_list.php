<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/sign_list.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);


// 输出页面参数
$goods_id = trim($_INPUT['goods_id']);
$stage_id = trim($_INPUT['stage_id']);
$page_params = mall_output_format_data(array
    (
        'goods_id'=>$goods_id,
        'stage_id'=>$stage_id,
    ));





$tpl->assign('page_params',$page_params);
$tpl->assign('wap_global_top', $wap_global_top);

$tpl->output();
?>