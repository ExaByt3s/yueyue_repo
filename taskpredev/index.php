<?php

/**
 * �жϿͻ���
 */

/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

// Ȩ���ļ�
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
    // ===============  ���� > 0 ���̼ң���������ʾ��ͬ��ͷ��  ===============
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