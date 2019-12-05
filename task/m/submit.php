<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./common_head.php');
$lead_id = $_INPUT['lead_id'];

$tpl = $my_app_pai->getView('submit.tpl.htm');

$task_lead_obj = POCO::singleton('pai_task_lead_class');
$lead_info = $task_lead_obj->get_lead_by_lead_id($lead_id);

$ret_code = $task_lead_obj -> check_user_auth($yue_login_id,$lead_id);
if(!$ret_code)
	{
		echo "<script type='text/javascript'>window.alert('非法操作');window.top.location.href='./list.php';</script>";	
		exit;
}

$tpl->assign('lead_info', $lead_info);

$tpl->assign('time', time());  //随机数

$tpl->output();
 ?>