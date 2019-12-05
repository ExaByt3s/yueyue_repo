<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./common_head.php');
$lead_id = $_INPUT['lead_id'];

$tpl = $my_app_pai->getView('details.tpl.htm');

$tpl->assign('time', time());  //�����

$task_lead_obj = POCO::singleton('pai_task_lead_class');
$lead_info = $task_lead_obj->get_lead_by_lead_id($lead_id);

$tpl->assign('lead_info', $lead_info);

// ������ʽ��js����
$m_task_top = $my_app_pai->webControl('m_task_top', array(), true);
$tpl->assign('m_task_top', $m_task_top);

// // ͷ������
$m_global_top = $my_app_pai->webControl('m_global_top', array(), true);
$tpl->assign('m_global_top', $m_global_top);

// // �ײ�����
$m_global_bot = $my_app_pai->webControl('m_global_bot', array(), true);
$tpl->assign('m_global_bot', $m_global_bot);

$ret_code = $task_lead_obj -> check_user_auth($yue_login_id,$lead_id);
if(!$ret_code)
	{
		echo "<script type='text/javascript'>window.alert('�Ƿ�����');window.top.location.href='http://www.yueus.com/task/m/list.php';</script>";	
		exit;
}
/*
 * ��ǰ�������ж���������
 * @param int $request_id
 * @param bool $b_select_count
 * @return int
 */
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$count_quote = $task_quotes_obj->get_quotes_list_for_valid($lead_info['request_id'], true);
$count_quote = (int)$count_quote;
$tpl->assign('count_quote', $count_quote);

/*
 * ��ȡ�û����⿨��
 * @param int $user_id
 * @return array
 */
 $task_coin_obj = POCO::singleton('pai_task_coin_class');
$coin_info = $task_coin_obj->get_coin_info($yue_login_id);
$balance = $coin_info['balance'];
$tpl->assign('balance', $balance);

/*
 * ��ȡ��ǰ������Ҫ�����⿨��
 * @param int $service_id
 * @return array
 */
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($lead_info['service_id']);
$pay_coins = $service_info['pay_coins'];
$tpl->assign('pay_coins', $pay_coins);
$tpl->assign('service_name', $service_info['service_name']);

/*
 * ��������鿴״̬
 * @param int $lead_id
 * @param int $user_id
 * @return int
 */
$task_lead_obj->update_is_read($lead_id,$yue_login_id);


/*
 * ��ȡ�����ʾ��ʴ�
 * @param int $request_id
 * @return array
 */
$obj = POCO::singleton('pai_task_questionnaire_class');
$arr = $obj->show_questionnaire_data($lead_info['request_id']);
$tpl->assign('ques_arr', $arr);

/*
���ֵ�ѡ��ѡ
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