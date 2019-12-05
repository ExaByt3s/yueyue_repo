<?php


/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);



// 通用
include_once($file_dir.'/./pc_common.inc.php');

// 权限文件
include_once($file_dir.'/./pc_auth_common.inc.php');

// 头部css相关
include_once($file_dir. '/./webcontrol/head.php');

// 顶部栏
include_once($file_dir. '/./webcontrol/global-top-bar.php');

// 底部
include_once($file_dir. '/./webcontrol/footer.php');

// 下载区域
include_once($file_dir. '/./webcontrol/down-app-area.php');


// ================== 载入模板 ==================
$tpl = $my_app_pai->getView('agree.tpl.htm');



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