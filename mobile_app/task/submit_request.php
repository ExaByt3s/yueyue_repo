<?php
/**
 * �ύ����
 * @author Henry
 * @copyright 2015-04-09
 */

$b_t = time();
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//��־ http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log(array(), 'submit_request_begin', 'task');

//��־ http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($client_data, 'submit_request_middle', 'task');

//��ȡ����
$user_id = intval($client_data['data']['param']['user_id']);
$service_id = intval($client_data['data']['param']['service_id']);
$question_list = $client_data['data']['param'];
if( !is_array($question_list) ) $question_list = array();

//��ȡ������Ϣ
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($service_id);
$title = trim($service_info['service_name']);

//��ȡ�û���Ϣ
$pai_user_obj = POCO::singleton('pai_user_class');
$user_info = $pai_user_obj->get_user_info($user_id);
$cellphone = trim($user_info['cellphone']);

//��������
$more_info = array(
	'title' => $title,
	'cellphone' => $cellphone,
	'email' => '',
);

$task_request_obj = POCO::singleton('pai_task_request_class');
$submit_ret = $task_request_obj->submit_request($user_id, $service_id, $more_info, $question_list);

$data = array();
$data['result'] = $submit_ret['result'];
$data['message'] = $submit_ret['message'];
$data['request_id'] = $submit_ret['request_id'];
$data['text1'] = '�����ύ����ɹ����Ժ�������֪ͨ��лл';
//$data['text1'] = '���ǻὫ��������͸����з����ʸ��' . $service_info['office_name'];
//$data['text2'] = '�����24Сʱ���յ����ۼ�����';
//$data['mid'] = '122OD04001'; //ģ��ID

//��־ http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($data, 'submit_request_end', 'task');

$e_t = time();
$diff_time = $et-$bt;
$data['diff_time'] = $diff_time;

pai_log_class::add_log($data, 'submit_request_time', 'submit_request_time');

$options['data'] = $data;
$cp->output($options);
