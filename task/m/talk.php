<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./common_head.php');
$tpl = $my_app_pai->getView('talk.tpl.htm');


// ������ʽ��js����
$m_task_top = $my_app_pai->webControl('m_task_top', array(), true);
$tpl->assign('m_task_top', $m_task_top);

// // ͷ������
$m_global_top = $my_app_pai->webControl('m_global_top', array(), true);
$tpl->assign('m_global_top', $m_global_top);

// // �ײ�����
$m_global_bot = $my_app_pai->webControl('m_global_bot', array(), true);
$tpl->assign('m_global_bot', $m_global_bot);

$tpl->assign('time', time());  //�����

$quotes_id = (int)$_INPUT['quotes_id'];


/*
 * ��ȡ���屨����Ϣ
 * @param int $quote_id
 * 
 * return array
 */
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$ret_code = $task_quotes_obj -> check_user_auth($yue_login_id,$quotes_id);
if(!$ret_code)
	{
		echo "<script type='text/javascript'>window.alert('�Ƿ�����');window.top.location.href='http://www.yueus.com/task/m/list.php';</script>";	
		exit;
}
$quote_info = $task_quotes_obj->get_quotes_detail_info_by_id($quotes_id);

$request_id = (int)$quote_info['request_id'];

/*
 * ��ȡ�����ʾ��ʴ�
 * @param int $request_id
 * @return array
 */
$obj = POCO::singleton('pai_task_questionnaire_class');
$arr = $obj->show_questionnaire_data($request_id);
$tpl->assign('sidebar_list', $arr);


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

/**
 * ��ȡ�����б������û�ID
 * @param int $user_id
 * @param int $quotes_id
 * @param string $b_select_count
 * @param string $where_str
 * @param string $order_by
 * @param string $limit
 * @return array|int
 */

$task_message_obj = POCO::singleton('pai_task_message_class');
$answer_list = $task_message_obj->get_message_list_by_user_id($yue_login_id, $quotes_id, $b_select_count=false, $order_by='message_id DESC', $limit='0,1000');
$tpl->assign('answer_tips', $answer_list[0]['message_content'] );

$tpl->assign('answer_list', $answer_list );

// print_r($answer_list);


$is_archive = $quote_info['is_archive'];

$task_request_obj = POCO::singleton('pai_task_request_class');
$request_detail_info = $task_request_obj->get_request_detail_info_by_id($request_id);


/**
 * ��ȡ�����̬��Ϣ
 * @param int $user_id
 * @param int $quotes_id
 * @return array
 */
$feed_info = $task_message_obj->get_feed_info_lately_by_user_id($yue_login_id, $quotes_id);


$tpl->assign('feed_info', $feed_info );


$tpl->assign('request_detail_info',$request_detail_info);

$tpl->assign('quote_info', $quote_info);
$tpl->assign('is_archive', $is_archive);
$tpl->assign('quotes_id', $quotes_id);

//�鿴�������³ɲ���Ҫ�����ң�
$task_quotes_obj->update_quotes_important($quotes_id, 0);

$tpl->output();
 ?>