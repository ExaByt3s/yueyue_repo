<?php 
/**
 * 添加评价
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$quote_id = intval($client_data['data']['param']['quote_id']);

//初始化对象
$task_quote_obj = POCO::singleton('pai_task_quotes_class');

$quote_info = $task_quote_obj->get_quotes_detail_info_by_id($quote_id);


$data['user_icon'] = get_user_icon($quote_info['user_id']);
$data['nickname'] = get_user_nickname_by_user_id($quote_info['user_id']);
$data['request_finish_time'] = '';
$data['is_hire'] = '0';

$options['data'] = $data;
$cp->output($options);

?>