<?php


// 注意： 此常量为首页使用 请勿拷贝
    define("index_value",1);
// 注意： 此常量为首页使用 end


include_once 'common.inc.php';


$pc_config = $_INPUT['pc'];


if((false!==stripos($_SERVER['HTTP_USER_AGENT'], 'android') || false!==stripos($_SERVER['HTTP_USER_AGENT'], 'iphone')) && empty($pc_config) )
{
    //wap版

    $pc_wap = 'wap/';

    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'index-page.tpl.htm');
}
else
{
    //Pc版

    $pc_wap = 'pc/';

    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'index-page.tpl.htm');

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

}


$tpl->assign('formal_is_hide', 1);  //正式版下载链接  1显示 0隐藏
$tpl->assign('wait_is_hide', 0);   //敬请期待         1显示 0隐藏



// iPhone版本下载
$tpl->assign('down_iPhone_app', 'http://app.yueus.com/dl_merchant_for_i.php');

// Android版本
$tpl->assign('down_android_app', 'http://app.yueus.com/dl_merchant_for_a.php');


$tpl->output();


?>