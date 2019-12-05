<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."audit_detail.tpl.htm" );

$request_id = $_INPUT['request_id'];
$act = $_INPUT['act'];
$again = $_INPUT['again'];
$send_type = $_INPUT['send_type'];
$user_id = $_INPUT['user_id'];
$remark = $_INPUT['remark'];
$not_pass_reason = $_INPUT['not_pass_reason'];


$obj = POCO::singleton('pai_task_questionnaire_class');
$task_request_obj = POCO::singleton('pai_task_request_class');
$request_info = $task_request_obj->get_request_detail_info_by_id($request_id);
$task_log_obj = POCO::singleton('pai_task_admin_log_class');
$lead_obj = POCO::singleton('pai_task_lead_class');

if($act=='pass')
{
	if($again == 'Y')
	{
		$ret=$task_request_obj->send_request_lead_by_artificial_again($request_id,$user_id);
		$task_log_obj->add_log($yue_login_id,1002,1,$_INPUT,$remark,$request_id);
	}
	else
	{
		if($send_type=='part')
		{
			$ret=$task_request_obj->send_request_lead_by_artificial($request_id,$user_id);
			$task_log_obj->add_log($yue_login_id,1001,1,$_INPUT,'',$request_id);
			$task_log_obj->add_log($yue_login_id,1002,1,$_INPUT,$remark,$request_id);
		}
		else
		{
			$ret=$task_request_obj->submit_lead_by_request_id_all($request_id);
			$task_log_obj->add_log($yue_login_id,1001,1,$_INPUT,'',$request_id);
			$task_log_obj->add_log($yue_login_id,1003,1,$_INPUT,$remark,$request_id);			
		}		
	}
	
	if($ret['result']==1)
	{
		if($request_info['lead_status'] == 0)//审核,修改过期时间
		{
			$task_request_obj->reset_request_expire_time($request_id);
			$task_log_obj->add_log($yue_login_id,1005,1,$_INPUT,'',$request_id);
		}		
		js_pop_msg("审核成功",false,"http://www.yueus.com/yue_admin/task/list.php");
	}
	else
	{
		js_pop_msg($ret['message']);
	}
}

if($act=='not_pass')
{
	$remark = $not_pass_reason."<br />".$remark;
	$data['admin_id'] = $yue_login_id;
	$data['admin_note'] = $remark;
	$ret = $task_request_obj->del_request($request_id,$data);
	$task_log_obj->add_log($yue_login_id,1004,1,$_INPUT,$remark,$request_id);
	js_pop_msg("操作成功",false,"http://www.yueus.com/yue_admin/task/list.php");
}



/*
 * 获取需求问卷问答
 * @param int $request_id
 * @return array
 */

$question = $obj->show_questionnaire_data($request_id);
$request_info['add_time'] = date("Y-m-d H:i",$request_info['add_time']);

$not_pass_config = include '/disk/data/htdocs232/poco/pai/config/task_not_pass_config.php';
foreach($not_pass_config as $k=>$val)
{
	$not_pass_text_arr[$k]['text'] = $val;
}


$task_admin_log_obj = POCO::singleton('pai_task_admin_log_class');
$where_log =array(
                  'action_type'=>1,
				  'action_id'=>$request_id,
				  );
$log = $task_admin_log_obj->get_log_by_type($where_log);
if($log)
{
	foreach($log as $key => $val)
	{
		$log[$key]['admin_name'] = get_user_nickname_by_user_id($val['admin_id']);
		$log[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
	}
}

if($request_info['lead_status']==1)
{
	$lead_list = $lead_obj->get_lead_list_by_request_id($request_id,false, '', '0,100000','add_time DESC', 'user_id');
	foreach ($lead_list as $val)
	{
		$has_send_user_arr[] = $val['user_id'];
	}
	$has_send_user = implode(",",$has_send_user_arr);
	$tpl->assign ( "has_send_user",$has_send_user );
}


$tpl->assign ( "request_info",$request_info );
$tpl->assign ( "question",$question );
$tpl->assign ( "not_pass_text_arr",$not_pass_text_arr );
$tpl->assign ( "log",$log );

$tpl->output ();

?>