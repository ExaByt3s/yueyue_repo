<?php

// include_once '../common.inc.php';
define('MALL_SELLER_IS_NOT_LOGIN',1);
include_once '../common.inc.php';




$pc_wap = 'pc/';
$tpl = $my_app_pai->getView('../'.TASK_TEMPLATES_ROOT.$pc_wap.'recruit/update_pay.tpl.htm');


// 头部css相关
include_once('../'.TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// 顶部栏
include_once('../'.TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// 底部
include_once('../'.TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// 头部bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// 底部
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);

if ($yue_login_id) 
{
    $is_login = 1 ;
}
else
{
    $is_login = 0 ;
}

$tpl->assign('is_login', $is_login);




$tpl->output();


?>