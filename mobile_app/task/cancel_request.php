<?php 
/**
 * ȡ�������б�
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//��ȡ����
$user_id = intval($client_data['data']['param']['user_id']);
$request_id = intval($client_data['data']['param']['request_id']);
$reason = trim($client_data['data']['param']['reason']);

//��ʼ������
$task_request_obj = POCO::singleton('pai_task_request_class');

$ret = $task_request_obj->cancel_request($request_id, $reason);


$data['code'] = $ret['result'];
$data['message'] = $ret['message'];

$options['data'] = $data;
$cp->output($options);

?>