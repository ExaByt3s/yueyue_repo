<?php
/**
 * 评论列表
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log(array(), 'get_review_begin', 'task');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$seller_user_id = intval($client_data['data']['param']['seller_user_id']);
$limit = $client_data['data']['param']['limit'];

//初始化对象
$task_review_obj = POCO::singleton('pai_task_review_class');

$count = $task_review_obj->get_user_review_list(true,$seller_user_id);
$list = $task_review_obj->get_user_review_list(false,$seller_user_id,$limit);

foreach($list as $k=>$val)
{
	$new_list[$k]['rank'] = $val['rank'];
	$new_list[$k]['content'] = $val['content'];
	$new_list[$k]['user_icon'] = get_user_icon($val['from_user_id']);
	$new_list[$k]['nickname'] = get_user_nickname_by_user_id($val['from_user_id']);
}

$data = array();
$data['review_list'] = $new_list ? $new_list : array();
$data['review_text'] = "已完成{$count}次交易";

//日志 http://yp.yueus.com/logs/201502/03_task.txt
pai_log_class::add_log($data, 'get_review_end', 'task');

$options['data'] = $data;
$cp->output($options);
