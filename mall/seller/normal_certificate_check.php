<?php

/**
 * 认证审核页，静态
 *
 *
 * 2015-6-16
 *
 *
 *  author    星星
 *
 *
 */

include_once 'common.inc.php';
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'normal_certificate_check.tpl.htm');

if(empty($yue_login_id))
{

    $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $r_url = urlencode($r_url);
    echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=".$r_url."';</script>";
    exit;
    //echo "not login error";
    //exit();

}
//校验用户角色,非商家报错
$user_id = $yue_login_id;


$check_type = trim($_INPUT['check_type']);
$check_type_array = array("certificate","service");
if(empty($check_type) || !in_array($check_type,$check_type_array))
{
    $check_type = "certificate";
}



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

$page_title = "认证审核";
$tpl->assign("page_title",$page_title);
$tpl->assign("check_type",$check_type);

$tpl->output();

?>