<?php
include_once 'common.inc.php';

$request_id = (int)$_INPUT['request_id'];
$act = $_INPUT['act'];
$send_type = $_INPUT['send_type'];
$user_id = $_INPUT['user_id'];
$remark = $_INPUT['remark'];

$task_request_obj = POCO::singleton('pai_task_request_class');
$request_info = $task_request_obj->get_request_detail_info_by_id($request_id);
if(!$request_info)
{
	exit;
}
$request_info['hired_time'] = $request_info['hired_time']?date('Y-m-d H:i:s',$request_info['hired_time']):"未雇佣";
$request_info['pay_time'] = $request_info['pay_time']?date('Y-m-d H:i:s',$request_info['pay_time']):"未支付";
$request_info['review_time'] = $request_info['review_time']?date('Y-m-d H:i:s',$request_info['review_time']):"未评价";

$obj = POCO::singleton('pai_task_questionnaire_class');
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$task_service_obj = POCO::singleton('pai_task_service_class');
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
//print_r($log);

/*
 * 获取需求问卷问答
 * @param int $request_id
 * @return array
 */
$question = $obj->show_questionnaire_data($request_id);
$request_info['add_time'] = date("Y-m-d H:i",$request_info['add_time']);
$quotes = $task_quotes_obj->get_quotes_detail_list_for_valid($request_id);
foreach($quotes as $key => $val)
{
	$quotes[$key]['pay_time'] = $val['pay_time']?date('Y-m-d H:i:s',$val['pay_time']):"";
	$quotes[$key]['user_data'] = $task_profile_obj->get_profile_info_by_id($val['profile_id']);
	$quotes[$key]['service_data'] = $task_service_obj->get_service_info($val['service_id']);
	$quotes[$key]['service_type'] = $val['service_id'] == 6?"线上":"线下";
	
}
//print_r($quotes);

$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."bill_detail.tpl.htm" );
$tpl->assign ( "request_info",$request_info );
$tpl->assign ( "question",$question );
$tpl->assign ( "quotes",$quotes );
$tpl->assign ( "log",$log );
$tpl->output ();

?>