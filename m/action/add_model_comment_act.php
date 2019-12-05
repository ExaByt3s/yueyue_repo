<?php 

/**
 * 评价模特
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}

$date_id = $_INPUT['date_id'];
$cameraman_user_id = $_INPUT['cameraman_user_id'];
$model_user_id = $_INPUT['model_user_id'];
$overall_score = $_INPUT['overall_score'];
$expressive_score = $_INPUT['expressive_score'];
$truth = mb_convert_encoding($_INPUT['truth'],'gbk','utf-8');
$time_sense = mb_convert_encoding($_INPUT['time_sense'],'gbk','utf-8');
$comment = mb_convert_encoding($_INPUT['comment'],'gbk','utf-8');
$is_anonymous = $_INPUT['is_anonymous'];


$model_comment_obj = POCO::singleton('pai_model_comment_log_class');

/*
 * 添加评价
 * 
 * @param int    $date_id    约拍ID
 * @param int    $cameraman_user_id 摄影师用户ID
 * @param int    $model_user_id     模特用户ID
 * @param enum   $overall_score     分数  1-5
 * @param enum   $expressive_score    分数  1-5
 * @param enum   $truth       真实或不真实
 * @param enum   $time_sense      准时或不准时
 * @param string $comment     评价
 * @param int    $is_anonymous     是否匿名评价
 * 
 * return int 
 */


if(empty($date_id) || empty($cameraman_user_id) || empty($model_user_id))
{
	die("参数错误");
}

$check_comment = $model_comment_obj->is_comment_by_cameraman($date_id, $cameraman_user_id);

if($check_comment){
	$output_arr['code'] = 0;
	$output_arr['msg'] = '你已评价该模特了';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

	mobile_output($output_arr,false);
	exit;
}

$ret = $model_comment_obj->add_comment($date_id, $cameraman_user_id, $model_user_id,$overall_score,$expressive_score,$truth,$time_sense, $comment,$is_anonymous);

$output_arr['code'] = $ret ? 1 :0;
$output_arr['msg'] = $ret ? '评价成功' : '评价失败';
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>