<?php
/** 
 * 
 * tt
 * ��Բ
 * 2015-4-11
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');
 
// Ȩ���ļ�
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');





include_once($file_dir. '/./webcontrol/footer.php');

$tpl = $my_app_pai->getView('order_detail.tpl.htm');


// �̼�ҳ ��������
include_once($file_dir.'/./seller_require.php');

$tpl->assign('time', time());  //�����


// ������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'order_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// �ײ�
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$task_lead_obj = POCO::singleton('pai_task_lead_class');



$tpl->assign('list', $lead_list);




$quotes_id = $_INPUT['quotes_id'];



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
$answer_list = $task_message_obj->get_message_list_by_user_id($yue_login_id, $quotes_id, $b_select_count=false, $order_by='message_id DESC', $limit='0,2000');

$tpl->assign('answer_list', $answer_list );


/**
 * ��ȡ�����̬��Ϣ
 * @param int $user_id
 * @param int $quotes_id
 * @return array
 */
$feed_info = $task_message_obj->get_feed_info_lately_by_user_id($yue_login_id, $quotes_id);


$tpl->assign('feed_info', $feed_info );

// print_r($answer_list);

/*
 * ��ȡ���屨����Ϣ
 * @param int $quote_id
 * 
 * return array
 */
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$re = $task_quotes_obj->check_user_auth($yue_login_id,$quotes_id);//��֤����Ȩ��
if(!$re)
{
	js_pop_msg("�Ƿ�����",false,"http://task.yueus.com/");
}
$quote_info = $task_quotes_obj->get_quotes_detail_info_by_id($quotes_id);
$quote_info['user_icon'] = yueyue_resize_act_img_url($quote_info['user_icon'], '32');
$is_archive = $quote_info['is_archive'];

//��ȡ������ϸ��Ϣ
$task_request_obj = POCO::singleton('pai_task_request_class');
$request_detail_info = $task_request_obj->get_request_detail_info_by_id($quote_info['request_id']);

$tpl->assign('quote_info', $quote_info);
$tpl->assign('is_archive', $is_archive);
$tpl->assign('quotes_id', $quotes_id);
$tpl->assign('request_detail_info', $request_detail_info);


// print_r($request_detail_info);

/*
 * ��ȡ�����ʾ��ʴ�
 * @param int $request_id
 * @return array
 */
$obj = POCO::singleton('pai_task_questionnaire_class');
$arr = $obj->show_questionnaire_data($quote_info['request_id']);
$tpl->assign('sidebar_list', $arr);
// print_r($arr);





//�鿴�������³ɲ���Ҫ�����ң�
$task_quotes_obj->update_quotes_important($quotes_id, 0);

$tpl->output();
 ?>