<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


function _ctlpc_task_header($attribs)
{
    
    global $tpl;
    global $my_app_pai;
    global $yue_login_id;

    $tpl->assign('_dev',time());

    $header_tpl	 = $my_app_pai->getView('./webcontrols/pc_task_header.tpl.htm',true);

    if (!$yue_login_id) {
        $header_tpl->assign('no_login', '1');
    }

	if(!defined("DONT_CHECK_AUTH"))
	{
	    //  û�е�¼��ȥ��¼
	    if (!$yue_login_id) {
	        header("Location: http://www.yueus.com/reg/login.php?r_url=http%3A%2F%2Fwww.yueus.com%2Ftask%2F"); 
	        exit() ;
	    }
	

	    // $get_all_profile_obj = POCO::singleton('pai_task_profile_class');
	    // $user_arr = $get_all_profile_obj->get_all_profile_user_id();
	
        $user_obj = POCO::singleton ( 'pai_user_class' );

        $get_all_profile_obj = POCO::singleton('pai_task_profile_class');
        $is_supplier = $get_all_profile_obj->check_seller_by_user_id($yue_login_id);


        if (!$is_supplier) 
        {
            $user_obj->logout();
            js_pop_msg("�����̼��˺ŵ�¼Ŷ��", false,"http://www.yueus.com/reg/login.php?r_url=http%3A%2F%2Fwww.yueus.com%2Ftask%2F");
        }

	    // if (!in_array($yue_login_id,$user_arr)) 
     //    {

     //        $user_obj->logout();
	    //     js_pop_msg("�����̼��˺ŵ�¼Ŷ��", false,"http://www.yueus.com/reg/login.php?r_url=http%3A%2F%2Fwww.yueus.com%2Ftask%2F");

	    // }
	    // print_r($user_arr);
    
	}


    // �û���Ϣ
    $user_icon = get_user_icon($yue_login_id);
    $user_nickname = get_user_nickname_by_user_id($yue_login_id);

    $header_tpl->assign('user_icon', $user_icon);
    $header_tpl->assign('user_nickname', $user_nickname);

    $header_tpl->assign('time', time());  //�����
    
    $header_tpl->assign('cur_page', $attribs['cur_page']);

    // $header_tpl->assign('title_txt', $attribs['title_txt']);
    
	$task_seller_obj = POCO::singleton('pai_task_seller_class');
	$seller_info = $task_seller_obj->get_seller_info($yue_login_id);
	
    // �̼���Ϣ  ��֤v
    $task_profile_obj = POCO::singleton('pai_task_profile_class');
    $profile_info = $task_profile_obj->get_profile_info($yue_login_id,$seller_info['service_id']);
    $is_vip = $profile_info['is_vip'];
    $header_tpl->assign('is_vip', $is_vip);  //�����
    // print_r($profile_info);

    
    //�������⿨
    if( !empty($seller_info) )
    {
    	$ref_id = strtotime(date('Y-m-d 00:00:00'));
    	$task_coin_obj = POCO::singleton('pai_task_coin_class');
    	$task_coin_obj->submit_give('SELLER_LOGIN_TODAY', $yue_login_id, $ref_id);
    }
    
    $header_html = $header_tpl->result();
    return $header_html;

}
?>