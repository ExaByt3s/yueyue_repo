<?php
/**
 * 删除需求
 * @author koko
 * @copyright 2015-04-30
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log(array(), 'del_request_begin', 'task');

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($client_data, 'del_request_middle', 'task');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$request_id = intval($client_data['data']['param']['request_id']);

$task_request_obj = POCO::singleton('pai_task_request_class');

$submit_ret = $task_request_obj->change_request_status_del($user_id, $request_id);

$data = array();
$data['result'] = $submit_ret['result'];
$data['message'] = $submit_ret['message'];

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($data, 'del_request_end', 'task');

$options['data'] = $data;
$cp->output($options);
