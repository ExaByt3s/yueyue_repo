<?php

/**
 * 首页模特数据
 * zy 2014.9.25
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$location_id = intval($_INPUT['location_id']);
$type = $_INPUT['type'];

$user_obj = POCO::singleton('pai_user_class');
$hot_model_obj = POCO::singleton('pai_hot_model_class');
$score_obj = POCO::singleton('pai_score_class');


$rel_page_count_comment = 6; //取数据
$page_count_comment = $rel_page_count_comment+1;

$rel_page_count_hot = 3;//取数据
$page_count_hot = $rel_page_count_hot+1;


if($page > 1)
{
	$limit_start_comment = ($page - 1)*($page_count_comment - 1);
}
else
{
	$limit_start_comment = ($page - 1)*$page_count_comment;
	$limit_start_comment = $limit_start_comment<0 ? 0 : $limit_start_comment;
}

$limit_comment = "{$limit_start_comment},{$page_count_comment}";


if($page > 1)
{
	$limit_start_hot = ($page - 1)*($page_count_hot - 1);
}
else
{
	$limit_start_hot = ($page - 1)*$page_count_hot;
	$limit_start_hot = $limit_start_hot<0 ? 0 : $limit_start_hot;
}

$limit_hot = "{$limit_start_hot},{$page_count_hot}";



switch($type){
	case "home_page":
		$ret['user_score_top'] = $score_obj->get_user_score_top($location_id, '0,6');

		foreach($ret['user_score_top'] as $k=>$val){
			$ret['user_score_top'][$k]['num'] = $val['num'].'分';
		}
		
		$ret['date_rank'] = get_date_rank($location_id,'0,6');

		foreach($ret['date_rank'] as $k=>$val){
			$ret['date_rank'][$k]['num'] = $val['num'].'次';
		}

		$ret['comment_score_top'] = $user_obj->get_model_comment_score_top(false,$location_id,$limit_comment);

		foreach($ret['comment_score_top'] as $k=>$val){
			$ret['comment_score_top'][$k]['num'] = $val['num'].'分';
		}

		$ret['hot_model'] = $hot_model_obj->get_hot_model(false,$location_id,$limit_hot);

		$comment_score_top_has_next_page = (count($ret['comment_score_top'])>$rel_page_count_comment);
		$hot_model_has_next_page = (count($ret['hot_model'])>$rel_page_count_hot);

		if($comment_score_top_has_next_page)
		{
			array_pop($ret['comment_score_top']);
		}
		$ret['comment_score_top_has_next_page'] = $comment_score_top_has_next_page;

		if($hot_model_has_next_page)
		{
			array_pop($ret['hot_model']);
		}
		$ret['hot_model_has_next_page'] = $hot_model_has_next_page;

	break;

	case "hot_model":
		$ret = $hot_model_obj->get_hot_model($b_select_count=false,$location_id,$limit_hot);
	break;

	case "comment_score_top":
		$ret = $user_obj->get_model_comment_score_top($b_select_count=false,$location_id,$limit_comment);
		foreach($ret as $k=>$val){
			$ret[$k]['num'] = $val['num'].'分';
		}
	break;

}



if($type=='hot_model'){

	$has_next_page = (count($ret)>$rel_page_count_hot);
	if($has_next_page)
	{
		array_pop($ret);
	}

	$output_arr['has_next_page'] = $has_next_page;

}elseif($type=='comment_score_top'){

	$has_next_page = (count($ret)>$rel_page_count_comment);
	if($has_next_page)
	{
		array_pop($ret);
	}

	$output_arr['has_next_page'] = $has_next_page;
}



$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '获取成功' : '获取失败';
$output_arr['list'] = $ret;
$output_arr['type'] = $type;

mobile_output($output_arr,false);





?>