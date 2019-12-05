<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$model_comment_log_obj 		= POCO::singleton ( 'pai_model_comment_log_class' );
$cameraman_comment_log_obj 	= POCO::singleton ( 'pai_cameraman_comment_log_class' );
$user_obj 					= POCO::singleton ( 'pai_user_class' );

$user_id 	= $client_data ['data'] ['param'] ['v_id'];
$limit 		= $client_data['data']['param']['limit'];

$role 	= $user_obj->check_role ( $user_id );
if ($role == 'model')
{
	$ret = $model_comment_log_obj->get_model_comment_list($user_id, false, $limit);
}
elseif($role='cameraman')
{
	$ret = $cameraman_comment_log_obj->get_cameraman_comment_list($user_id, false, $limit);
}

foreach($ret as $k=>$val)
{
	$new_ret[$k]['u_name'] 	= $val['nickname'];
	$new_ret[$k]['comment'] 	= $val['comment'];
	$new_ret[$k]['rating'] 	= $val['overall_score'];
	$new_ret[$k]['time'] 		= $val['add_time'];
}
$data['mid'] 		= '';
$data['title'] 	= 'ำรปงฦภผÛ';
$data['data'] 		= $new_ret;

$options ['data'] = $data;
$cp->output ( $options );
?>