<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./mine_head.php');
$tpl = $my_app_pai->getView('mine_request.tpl.htm');

$yue_login_id = intval($_INPUT['user_id'])?intval($_INPUT['user_id']) : $yue_login_id;

//头部引入
$m_global_top = $my_app_pai->webControl('m_global_top', array(), true);
$tpl->assign('m_global_top', $m_global_top);

$task_seller_obj = POCO::singleton('pai_task_seller_class');
$seller_info = $task_seller_obj->get_seller_info($yue_login_id);
if( empty($seller_info) )
{
	js_pop_msg("必须是商家账号哦！", false, "http://task.yueus.com/");
}
$service_id = intval($seller_info['service_id']);

/*
 * 获取商家FAQ
 * @param int $profile_id 商家ID
 * @param string $limit 
 * return array
 */
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$profile_info = $task_profile_obj->get_profile_info($yue_login_id, $service_id);
$faq = $task_profile_obj->get_profile_faq_list($profile_info['profile_id'],$limit='0,1000');
$tpl->assign('faq',$faq);
//print_r($faq);

$tpl->assign('time', time());  //随机数


$tpl->output();
 ?>