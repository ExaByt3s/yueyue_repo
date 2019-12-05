<?php
/**
 * 提交留言
 * @author rong
 * @copyright 2015-04-09
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log(array(), 'submit_message_begin', 'task');

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($client_data, 'submit_message_middle', 'task');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$quote_id = intval($client_data['data']['param']['quote_id']);
$content = $client_data['data']['param']['content'];

$content=str_replace('\n',"\n",$content);
$content = htmlspecialchars($content);

$task_message_obj = POCO::singleton('pai_task_message_class');
$submit_ret = $task_message_obj->submit_message($user_id, $quote_id, 'message', $content, $more_info=array());

$data['code'] = $submit_ret['result'];
$data['message'] = $submit_ret['message'];

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($data, 'submit_message_end', 'task');

$options['data'] = $data;
$cp->output($options);
