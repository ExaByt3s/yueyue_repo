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
$rank = intval($client_data['data']['param']['rank']);
$content = $client_data['data']['param']['content'];



//初始化对象
$task_review_obj = POCO::singleton('pai_task_review_class');
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');

$quotes_info = $task_quotes_obj->get_quotes_info($quote_id);

$insert_data['quotes_id'] = $quote_id;
$insert_data['request_id'] = $quotes_info['request_id'];
$insert_data['from_user_id'] = $user_id;
$insert_data['profile_id'] = $quotes_info['profile_id'];
$insert_data['to_user_id'] = $quotes_info['user_id'];
$insert_data['rank'] = $rank;
$insert_data['content'] = str_replace('\n',"\n",$content);

$ret = $task_review_obj->add_review($insert_data);


$data['code'] = $ret['result'];
$data['message'] = $ret['message'];


$options['data'] = $data;
$cp->output($options);

?>