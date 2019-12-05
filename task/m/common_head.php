<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

  //  没有登录跳去登录
    if (!$yue_login_id) {
        header('location:login.php?1');
        exit() ;
    }

	// pai_task_profile_class
    /*
     * 获取所有商家用户ID
     * @return array
     */
/*
    $get_all_profile_obj = POCO::singleton('pai_task_profile_class');
    $user_arr = $get_all_profile_obj->get_all_profile_user_id();

    if (!in_array($yue_login_id,$user_arr)) {
        js_pop_msg("必须商家账号登录哦！", false,"http://www.yueus.com/");
    }
    //print_r($user_arr);
	*/


	$user_obj = POCO::singleton ( 'pai_user_class' );

    $get_all_profile_obj = POCO::singleton('pai_task_profile_class');
    $is_supplier = $get_all_profile_obj->check_seller_by_user_id($yue_login_id);


        if (!$is_supplier) 
        {
            $user_obj->logout();
            js_pop_msg("必须商家账号登录哦！", false,"http://www.yueus.com/task/m/login.php");
        }

 ?>