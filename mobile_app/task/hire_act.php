<?php 
/**
 * ��Ӷ
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//��־ http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log(array(), 'hire_act_begin', 'task');

//��ȡ����
$user_id = intval($client_data['data']['param']['user_id']);
$quote_id = intval($client_data['data']['param']['quote_id']);

//��ʼ������
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');


$ret = $task_quotes_obj->hire_quotes($quote_id);


$data['code'] = $ret['result'];
$data['message'] = $ret['message'];

//��־ http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($data, 'hire_act_end', 'task');

$options['data'] = $data;
$cp->output($options);

?>