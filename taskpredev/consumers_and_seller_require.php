<?php
/** 
 * 
 * 消费者与商家都能看到的页面，引入此文件
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