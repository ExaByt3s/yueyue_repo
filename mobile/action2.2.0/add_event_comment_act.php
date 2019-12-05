<?php 

/**
 * 评价活动
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}



$event_id = $_INPUT['event_id'];
$table_id = $_INPUT['table_id'];
$user_id = $_INPUT['user_id'];
$overall_score = $_INPUT['overall_score'];
$organize_score = $_INPUT['organize_score'];
$model_score = $_INPUT['model_score'];
$comment = iconv("UTF-8", "gbk//TRANSLIT", $_INPUT['comment']);
$is_anonymous = $_INPUT['is_anonymous'];

if($user_id != $yue_login_id)
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = '非法用户';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

	mobile_output($output_arr,false);
	exit;
}

$event_comment_obj = POCO::singleton('pai_event_comment_log_class');

/*
 * 添加评价
 * 
 * @param int    $event_id    活动ID
 * @param int    $user_id 用户ID
 * @param enum   $overall_score     分数  1-5
 * @param enum   $organize_score    分数  1-5
 * @param enum   $model_score       分数  1-5
 * @param string $comment     评价
 * @param int    $is_anonymous     是否匿名评价
 * 
 * return int 
 */


if(empty($event_id) || empty($user_id))
{
	die("参数错误");
}

$check_comment = $event_comment_obj->is_event_comment_by_user($event_id, $table_id,$yue_login_id);

if($check_comment){
	$output_arr['code'] = 0;
	$output_arr['msg'] = '你已评价该活动了';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

	mobile_output($output_arr,false);
	exit;
}


$ret = $event_comment_obj->add_comment($event_id, $table_id,$user_id,$overall_score,$organize_score,$model_score, $comment,$is_anonymous);

$output_arr['code'] = $ret ? 1 :0;
$output_arr['msg'] = $ret ? '评价成功' : '评价失败';
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>