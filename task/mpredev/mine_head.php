<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//$yue_login_id = $_INPUT['user_id'] ? (int)$_INPUT['user_id'] : $yue_login_id;

(int)$_INPUT['user_id'] ? $visit = true : $visit = false;

if($visit)
{
	//�ο�
	$yue_login_id = $_INPUT['user_id'];
}
else
{
	//  û�е�¼��ȥ��¼
    if (!$yue_login_id) {
        echo "<script>window.location =\"./login.php\";</script>";
        exit() ;
    }

    $seller_profile_obj = POCO::singleton('pai_task_seller_class');  
	/**
	 * ��ȡ��Ϣ
	 * @param int $user_id
	 * @return array
	 */
	$ret = $seller_profile_obj->get_seller_info($yue_login_id);

	define($user_id , $_INPUT['user_id']);

	

    if (!$ret) {
        js_pop_msg("�����̼��˺ŵ�¼Ŷ��", false,"http://www.yueus.com/");
    }
    //print_r($user_arr);
}
  

 ?>