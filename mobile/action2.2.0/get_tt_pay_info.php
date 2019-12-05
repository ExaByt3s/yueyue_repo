<?php

/**
 *  获取tt支付信息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	die('no login');
}

$quotes_id = intval($_INPUT['quotes_id']);

//获取报价信息
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$quotes_detail_info = $task_quotes_obj->get_quotes_detail_info_by_id($quotes_id);

//获取服务信息
$service_id = intval($quotes_detail_info['service_id']);
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($service_id);

//获取个人信息
$obj = POCO::singleton('pai_user_class');
$ret = $obj->get_user_info_by_user_id($yue_login_id);

$quotes_detail_info['user_id'] = $ret['user_id'];
$quotes_detail_info['service_name'] = trim($service_info['service_name']);
$quotes_detail_info['pay_type'] = '服务金';
$quotes_detail_info['available_balance'] =  $ret['available_balance']; //$ret['available_balance'];
$quotes_detail_info['bail_available_balance'] = $ret['bail_available_balance'];
$quotes_detail_info['balance'] = $ret['balance'];  

 
mobile_output($quotes_detail_info,false);

?>