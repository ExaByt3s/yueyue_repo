<?php

/**
 * 判断客户端
 */

/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

// 权限文件
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');


$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;

if($__is_weixin || $__is_android || $__is_iphone) 
{
	header("Location: http://www.yueus.com/task/m/list.php");
}
else
{
    // ===============  根据 > 0 是商家，消费者显示不同的头部  ===============
    $task_seller_obj = POCO::singleton('pai_task_seller_class');
    $get_seller_info = $task_seller_obj->get_seller_info($yue_login_id);
    if (count($get_seller_info) > 0) 
    {
        header("Location: ./lead_list.php");
    }
    else
    {
        header("Location: ./request_list.php");
    }
    // =============== end ===============
    // 
	// header("Location: ./lead_list.php");
}

?>