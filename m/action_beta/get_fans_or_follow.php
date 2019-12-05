<?php
/**
 * hudw 2014.8.11
 * 首页热门图片列表
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;

if(empty($page))
{
	$page = 1;
}

$type = trim($_INPUT['type']);
$user_id = intval($_INPUT['user_id']) ;


// 分页使用的page_count
$page_count = 22;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}
$limit = "{$limit_start},{$page_count}";


/*
 * 获取热门模特内容
 * @param int $location_id
 * @param string $limit
 * 
 * return array
 */

$pai_user = POCO::singleton('pai_user_follow_class');

if($type == 'fans')
{
	/*
	* 根据用户ID获取被关注人数
	* @param int $user_id
	* @param bool $b_select_count
	* @param string $limit 
	*/
	$ret = $pai_user->get_user_be_follow_by_user_id ($user_id,$b_select_count=false,$limit);
}
else
{
	/*
	 * 根据用户ID获取关注人数
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $limit 
	 */
	$ret = $pai_user->get_user_follow_by_user_id($user_id,$b_select_count=false,$limit);

	
}



/**
 * 边图是特殊图，其他是方图
 * @var [type]
 */
/**/
foreach ($ret as $key => $value) 
{
	// 第四个为大图
	if(($key+1)%3==0)
	{
		$ret[$key]['type'] = 'special';			
	}
	else
	{
		$ret[$key]['type'] = 'one';	
	}

	
	
}

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 21;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

$output_arr['has_next_page'] = $has_next_page;
$output_arr['list'] = $ret;
$output_arr['title'] = false;


mobile_output($output_arr,false);
?>