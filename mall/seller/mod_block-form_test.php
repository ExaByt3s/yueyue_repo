<?php

/**
 * 商品服务操作页（添加或者编辑）
 *
 * 2015-6-17
 *
 * author  星星
 *
 */
include_once 'common.inc.php';


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$mall_obj = POCO::singleton('pai_mall_seller_class');


$goods_id = (int)$_INPUT['goods_id']; //接受参数

// 数据校验


$pc_wap = 'pc/';


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'mod-block-form-test.htm');




// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// 顶部栏
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// 头部bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);





// 底部
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);






$tpl->output();
?>