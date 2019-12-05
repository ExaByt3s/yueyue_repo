<?php
/**
 * 提交需求
 * @author Henry
 * @copyright 2015-04-09
 */

$b_t = time();
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log(array(), 'submit_request_begin', 'task');

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($client_data, 'submit_request_middle', 'task');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$service_id = intval($client_data['data']['param']['service_id']);
$question_list = $client_data['data']['param'];
if( !is_array($question_list) ) $question_list = array();

//获取服务信息
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($service_id);
$title = trim($service_info['service_name']);

//获取用户信息
$pai_user_obj = POCO::singleton('pai_user_class');
$user_info = $pai_user_obj->get_user_info($user_id);
$cellphone = trim($user_info['cellphone']);

//整理资料
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
$data['text1'] = '你已提交需求成功，稍后请留意通知，谢谢';
//$data['text1'] = '我们会将你的需求发送给所有符合资格的' . $service_info['office_name'];
//$data['text2'] = '你会在24小时内收到报价及介绍';
//$data['mid'] = '122OD04001'; //模板ID

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($data, 'submit_request_end', 'task');

$e_t = time();
$diff_time = $et-$bt;
$data['diff_time'] = $diff_time;

pai_log_class::add_log($data, 'submit_request_time', 'submit_request_time');

$options['data'] = $data;
$cp->output($options);
