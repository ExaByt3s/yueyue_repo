<?php
/** 
 * 
 * 商家页面 必须引入模块
 * 汤圆
 * 2015-5-27
 * common
 */



// ===============  根据 > 0 是商家，消费者显示不同的头部  ===============
$task_seller_obj = POCO::singleton('pai_task_seller_class');
$get_seller_info = $task_seller_obj->get_seller_info($yue_login_id);
if (count($get_seller_info) > 0) 
{
    include_once($file_dir. '/./webcontrol/top_nav.php');
}
else
{

    $local_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $encode_url = urlencode($local_url);
    $user_obj->logout();
    js_pop_msg("必须商家账号登录哦！", false,("http://www.yueus.com/reg/login.php?r_url=".$encode_url));

    include_once($file_dir. '/./webcontrol/consumers_top_nav.php');
}
// =============== end ===============

// ===============  根据用户身份，显示头部用户信息，消费者与商家  =============== 

if (count($get_seller_info) > 0) 
{
    $seller_id = $get_seller_info['user_id'];
    $tpl->assign('seller_id', $seller_id);
}
else
{
    $tpl->assign('seller_id', 0);
}

// ===============  根据用户身份，显示头部用户信息，消费者与商家 end  ============


?>