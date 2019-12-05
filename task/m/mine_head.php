<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//$yue_login_id = $_INPUT['user_id'] ? (int)$_INPUT['user_id'] : $yue_login_id;

(int)$_INPUT['user_id'] ? $visit = true : $visit = false;

if($visit)
{
	//游客
	$yue_login_id = $_INPUT['user_id'];
}
else
{
	//  没有登录跳去登录
    if (!$yue_login_id) {
        echo "<script>window.location =\"./login.php\";</script>";
        exit() ;
    }

    $seller_profile_obj = POCO::singleton('pai_task_seller_class');  
	/**
	 * 获取信息
	 * @param int $user_id
	 * @return array
	 */
	$ret = $seller_profile_obj->get_seller_info($yue_login_id);

	define($user_id , $_INPUT['user_id']);

	

    if (!$ret) {
        js_pop_msg("必须商家账号登录哦！", false,"http://www.yueus.com/");
    }
    //print_r($user_arr);
}
  

 ?>