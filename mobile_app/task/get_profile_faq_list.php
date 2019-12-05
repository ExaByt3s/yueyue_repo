<?php
/**
 * 商家常见问题列表
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$profile_id = intval($client_data['data']['param']['profile_id']);

//初始化对象
$task_profile_obj = POCO::singleton('pai_task_profile_class');


$list = $task_profile_obj->get_profile_faq_list($profile_id);

$data = array();
$data['faq_text'] = "深入了解我";
$data['faq_list'] = $list;

$options['data'] = $data;
$cp->output($options);
