<?php 

/**
 * 评价摄影师
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
$rp_score = $_INPUT['rp_score'];
$time_sense = mb_convert_encoding($_INPUT['time_sense'],'gbk','utf-8');
$comment = iconv("UTF-8", "gbk//TRANSLIT", $_INPUT['comment']);
$is_anonymous = $_INPUT['is_anonymous'];


$cameraman_comment_obj = POCO::singleton('pai_cameraman_comment_log_class');

/*
 * 添加评价
 * 
 * @param int    $date_id    约拍ID
 * @param int    $model_user_id     模特用户ID
 * @param int    $cameraman_user_id 摄影师用户ID
 * @param enum   $overall_score     分数  1-5
 * @param enum   $rp_score    分数  1-5
 * @param enum   $time_sense       准时或不准时
 * @param string $comment     评价
 * @param int    $is_anonymous     是否匿名评价
 * 
 * return int 
 */


if(empty($date_id) || empty($cameraman_user_id) || empty($model_user_id))
{
	die("参数错误");
}


$check_comment = $cameraman_comment_obj->is_comment_by_model($date_id, $model_user_id);

if($check_comment){
	$output_arr['code'] = 0;
	$output_arr['msg'] = '你已评价该摄影师了';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

	mobile_output($output_arr,false);
	exit;
}


$ret = $cameraman_comment_obj->add_comment($date_id, $model_user_id, $cameraman_user_id,$overall_score,$rp_score,$time_sense, $comment,$is_anonymous);

$output_arr['code'] = $ret ? 1 :0;
$output_arr['msg'] = $ret ? '评价成功' : '评价失败';
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>