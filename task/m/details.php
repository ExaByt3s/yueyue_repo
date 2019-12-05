<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./common_head.php');
$lead_id = $_INPUT['lead_id'];

$tpl = $my_app_pai->getView('details.tpl.htm');

$tpl->assign('time', time());  //随机数

$task_lead_obj = POCO::singleton('pai_task_lead_class');
$lead_info = $task_lead_obj->get_lead_by_lead_id($lead_id);

$tpl->assign('lead_info', $lead_info);

// 公共样式和js引入
$m_task_top = $my_app_pai->webControl('m_task_top', array(), true);
$tpl->assign('m_task_top', $m_task_top);

// // 头部引入
$m_global_top = $my_app_pai->webControl('m_global_top', array(), true);
$tpl->assign('m_global_top', $m_global_top);

// // 底部引入
$m_global_bot = $my_app_pai->webControl('m_global_bot', array(), true);
$tpl->assign('m_global_bot', $m_global_bot);

$ret_code = $task_lead_obj -> check_user_auth($yue_login_id,$lead_id);
if(!$ret_code)
	{
		echo "<script type='text/javascript'>window.alert('非法操作');window.top.location.href='http://www.yueus.com/task/m/list.php';</script>";	
		exit;
}
/*
 * 当前需求已有多少条报价
 * @param int $request_id
 * @param bool $b_select_count
 * @return int
 */
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$count_quote = $task_quotes_obj->get_quotes_list_for_valid($lead_info['request_id'], true);
$count_quote = (int)$count_quote;
$tpl->assign('count_quote', $count_quote);

/*
 * 获取用户生意卡数
 * @param int $user_id
 * @return array
 */
 $task_coin_obj = POCO::singleton('pai_task_coin_class');
$coin_info = $task_coin_obj->get_coin_info($yue_login_id);
$balance = $coin_info['balance'];
$tpl->assign('balance', $balance);

/*
 * 获取当前服务需要的生意卡数
 * @param int $service_id
 * @return array
 */
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($lead_info['service_id']);
$pay_coins = $service_info['pay_coins'];
$tpl->assign('pay_coins', $pay_coins);
$tpl->assign('service_name', $service_info['service_name']);

/*
 * 更改需求查看状态
 * @param int $lead_id
 * @param int $user_id
 * @return int
 */
$task_lead_obj->update_is_read($lead_id,$yue_login_id);


/*
 * 获取需求问卷问答
 * @param int $request_id
 * @return array
 */
$obj = POCO::singleton('pai_task_questionnaire_class');
$arr = $obj->show_questionnaire_data($lead_info['request_id']);
$tpl->assign('ques_arr', $arr);

/*
区分单选多选
*/
$single;
$more;
$info_count = 0;
$i = 0;
$k = 0;
foreach ($arr['data'] as $value) { 
	$count = (int)count($value['data']);
	if($count == 1){
		$single[$i] = $value;
		$i++;
	}
		elseif($count != 0){
			$more[$k] = $value; 
			$k++;
		}
		$info_count++;
}
$tpl->assign('single', $single);
$tpl->assign('more', $more);

$task_request_obj = POCO::singleton('pai_task_request_class');
$request_detail_info = $task_request_obj->get_request_detail_info_by_id($lead_info['request_id']);
if( in_array($request_detail_info['status_code'], array('started', 'introduced')) )
{
	$tpl->assign('status', 'ava');
}
else
{
	$tpl->assign('status', 'no');
}



$tpl->output();
 ?>