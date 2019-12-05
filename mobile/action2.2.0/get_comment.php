<?php 

/**
 * 评价模特列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$page = intval($_INPUT['page']);
$type = trim($_INPUT['type']);
$type_id = intval($_INPUT['type_id']);
$user_id = intval($_INPUT['user_id']);

// 分页使用的page_count
$page_count = 9;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}

$limit = "{$limit_start},{$page_count}";

switch ($type) 
{
	case 'model':
		$model_comment_obj = POCO::singleton('pai_model_comment_log_class');

		$ret = $model_comment_obj->get_model_comment_list($type_id,false,$limit);
		break;
	case 'cameraman':
		$cameraman_comment_obj = POCO::singleton('pai_cameraman_comment_log_class');

		$ret = $cameraman_comment_obj->get_cameraman_comment_list($type_id,false,$limit);
		break;
	case 'event':
		$event_comment_obj = POCO::singleton('pai_event_comment_log_class');
		//临时
		$event_info = get_event_list ( "event_id={$type_id}" );
		$user_id = $event_info[0]['user_id'];
		$ret = $event_comment_obj->get_user_comment_list($user_id,false,$limit);
		break;		
}

foreach($ret as $k=>$val){

	// 评分星星
	$has_star = intval($val['overall_score']);
	$miss_star = 5 - $has_star;

	for ($i=0; $i < 5; $i++) 
	{
		if($has_star>0)
		{
			$ret[$k]['stars_list'][$i]['is_red'] = 1; 	

			$has_star--;
		}
		else
		{
			$ret[$k]['stars_list'][$i]['is_red'] = 0; 	

			$miss_star--;						
		}
	}
} 

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 8;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}


$output_arr['list'] = $ret;

$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);

?>