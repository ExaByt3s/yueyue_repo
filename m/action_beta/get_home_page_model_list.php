<?php 

/**
 * 首页获取热门模特列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}

$location_id = (int)$_INPUT['location_id'];
$page = (int)$_INPUT['page'];

$hot_model_obj = POCO::singleton('pai_hot_model_class');
$user_obj = POCO::singleton('pai_user_class');


// 分页使用的page_count

$page_count = 7;

if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);
}
else
{
	$limit_start = ($page - 1)*$page_count;
}


$limit = "{$limit_start},{$page_count}";


switch($type){
	case "hot":
		$ret = $hot_model_obj->get_hot_model($b_select_count=false,$location_id,$limit);
	break;

	case "comment_score":
		$ret = $user_obj->get_model_comment_score_top($b_select_count=false,$location_id,$limit);
	break;
}



// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 6;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}


$output_arr['list'] = $ret;

$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);

?>