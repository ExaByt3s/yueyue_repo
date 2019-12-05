<?php 

/**
 * 模特积分排行榜列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



$page = intval($_INPUT['page']);

$location_id = (int)$_INPUT['location_id'];

$poco_cache_obj = new poco_cache_class ();

$cache_key = CACHE_RAMDOM."__YUEYUE_APP_MODEL_SCORE_RANK___".$location_id.$page;
//$cache = $poco_cache_obj->get_cache ( $cache_key );

if(!$cache){


	// 分页使用的page_count
	$page_count = 11;
	if($page > 1)
	{
		$limit_start = ($page - 1)*($page_count - 1);	
	}
	else
	{
		$limit_start = 0;	
	}

	$limit = "{$limit_start},{$page_count}";


	$score_obj = POCO::singleton('pai_score_rank_class');


	$ret = $score_obj->get_score_rank_list($location_id, $limit);


	// 输出前进行过滤最后一个数据，用于真实输出
	$rel_page_count = 10;

	$has_next_page = (count($ret)>$rel_page_count);

	if($has_next_page)
	{
		array_pop($ret);
	}


	$output_arr['list'] = $ret;

	$output_arr['has_next_page'] = $has_next_page;

	$poco_cache_obj->save_cache ( $cache_key, $output_arr, 3600 );

}else{
	$output_arr = $cache;
}

mobile_output($output_arr,false);

?>