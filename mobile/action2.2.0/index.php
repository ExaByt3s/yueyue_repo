<?php

/**
 * 首页模特数据
 * zy 2014.9.25
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$location_id = intval($_INPUT['location_id']);
$type = $_INPUT['type'];


$poco_cache_obj = new poco_cache_class ();

$cache_key = CACHE_RAMDOM."_YUEYUE_APP_INDEX____".$type.$location_id.$page;
//$cache = $poco_cache_obj->get_cache ( $cache_key );


if(!$cache){

	$user_obj = POCO::singleton('pai_user_class');
	$hot_model_obj = POCO::singleton('pai_hot_model_class');
	$goddess_model_obj = POCO::singleton('pai_goddess_model_class');
	$score_obj = POCO::singleton('pai_score_rank_class');
	$date_rank_obj = POCO::singleton('pai_date_rank_class');
	$comment_score_rank_obj = POCO::singleton('pai_comment_score_rank_class');

	$rel_page_count_comment = 6; //取数据
	$page_count_comment = $rel_page_count_comment+1;

	$rel_page_count_hot = 120;//取数据
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

			//$ret['user_score_top'] = $score_obj->get_score_rank_list($location_id, '0,6');
			$ret['user_score_top_title']= "魅力排行";
			foreach($ret['user_score_top'] as $k=>$val){
				$ret['user_score_top'][$k]['num'] = $val['num'];
				$ret['user_score_top'][$k]['unit'] = '分';
			}
			
			//$ret['date_rank'] = $date_rank_obj->get_date_rank($location_id,'0,6');
			$ret['date_rank_title']= "约拍排行";
			foreach($ret['date_rank'] as $k=>$val){
				$ret['date_rank'][$k]['num'] = $val['num'];
				$ret['date_rank'][$k]['unit'] = '次';
			}
			$ret['comment_score_top_title']= "优评推荐";
			//$ret['comment_score_top'] = $comment_score_rank_obj->get_comment_rank($location_id,$limit_comment);

			foreach($ret['comment_score_top'] as $k=>$val){
				$ret['comment_score_top'][$k]['num'] = $val['num']*2;
				$ret['comment_score_top'][$k]['unit'] = '分';
			}					
			
			 
			$ret['hot_model_title']= "热门模特";
			$ret['hot_model'] = $hot_model_obj->get_hot_model_beta(false,$location_id,$limit_hot);
			
			$ret['goddess_model1_title']= "约女神第一季";
			//$ret['goddess_model1'] = $goddess_model_obj->get_hot_model(false,$location_id,'1',$limit_hot);
			
			$ret['goddess_model2_title']= "约女神第二季";
			//$ret['goddess_model2'] = $goddess_model_obj->get_hot_model(false,$location_id,'2',$limit_hot);

			$ret['goddess_model3_title']= "约女神第三季";
			//$ret['goddess_model3'] = $goddess_model_obj->get_hot_model(false,$location_id,'3',$limit_hot);
			
			$ret['garden_model_title'] = "田园风格私拍专场";
			//$ret['garden_model'] = $goddess_model_obj->get_hot_model(false,$location_id,'garden_goddess',$limit_hot);
			
			$comment_score_top_has_next_page = (count($ret['comment_score_top'])>$rel_page_count_comment);
			$hot_model_has_next_page = (count($ret['hot_model'])>$rel_page_count_hot);
			$goddess_model_has_next_page = (count($ret['goddess_model'])>$rel_page_count_hot);

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

			if($goddess_model_has_next_page)
			{
				array_pop($ret['goddess_model']);
			}
			$ret['goddess_model_has_next_page'] = $goddess_model_has_next_page;


			$ret['home_page'] = true;



			if( $ret['hot_model'] ||$ret['user_score_top'] ||$ret['date_rank'] ||$ret['comment_score_top']){
				$output_arr['empty'] = false;
			}else{
				$output_arr['empty'] = true;
			}
			
		break;

		case "hot_model":
			$ret = $hot_model_obj->get_hot_model_beta(false,$location_id,$limit_hot);
			foreach($ret as $k=>$val){
				$ret[$k]['num'] = $val['num'];
				$ret[$k]['unit'] = '分';
				$ret[$k]['hot_model'] = true;
			}

			
		break;

		case "goddess_model":
			$ret = $goddess_model_obj->get_hot_model($b_select_count=false,$location_id,$limit_hot);
			foreach($ret as $k=>$val){
				$ret[$k]['num'] = $val['num'];
				$ret[$k]['unit'] = '分';
				$ret[$k]['hot_model'] = true;
			}

		break;

		case "comment_score_top":
			$ret = $comment_score_rank_obj->get_comment_rank($location_id,$limit_comment);
			foreach($ret as $k=>$val){
				$ret[$k]['num'] = $val['num']*2;
				$ret[$k]['unit'] = '分';
				$ret[$k]['comment_score_top'] = true;
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
	}elseif($type=='goddess_model'){

		$has_next_page = (count($ret)>$rel_page_count_hot);
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

	$poco_cache_obj->save_cache ( $cache_key, $output_arr, 3600 );

}else{
	$output_arr = $cache;
}


mobile_output($output_arr,false);





?>