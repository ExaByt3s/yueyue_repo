<?php

/**
 * 编辑服务选择，静态
 *
 *
 * 2015-6-17
 *
 *
 *  author    星星
 *
 *
 */

include_once 'common.inc.php';
$pc_wap = 'pc/';

$type = trim($_INPUT['type']);


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'app_guide.tpl.htm');
//校验用户角色,非商家报错
$user_id = $yue_login_id;

$guide_type_arr = array("order","money");
if(!in_array($type,$guide_type_arr))
{
    $type = "order";
}

if($type=="money")
{
    //to do:获取用户的金钱
    // 账号余额
    $pai_payment_obj = POCO::singleton('pai_payment_class');
    $user_available_balance = $pai_payment_obj->get_user_available_balance($user_id);
    $tpl->assign('user_available_balance', $user_available_balance);



    $page_title = "提现引导页";
    $nav_guide = "收入管理";

}
else
{
    $page_title = "订单引导页";
    $nav_guide = "订单管理";
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




$tpl->assign('type', $type);
$tpl->assign('page_title', $page_title);
$tpl->assign('nav_guide', $nav_guide);

//echo $yue_login_id;
//print_r($type_list);


$tpl->output();

?>