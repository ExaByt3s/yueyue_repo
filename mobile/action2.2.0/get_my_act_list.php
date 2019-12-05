<?php

/**
 * 获取我的活动列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;
$type = trim($_INPUT['type']);

if(empty($page))
{
	$page = 1;
}


// 分页使用的page_count
$page_count = 6;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}

$limit = "{$limit_start},{$page_count}";

//$yue_login_id = 53354078;

switch ($type) 
{
	case 'unpaid':				
	case 'paid':	
		/*
		 * 获取用户活动报名状态列表
		 * @param int $user_id
		 * @param string $status 未付款：unpaid 已付款：paid
		 * @param bool $b_select_count
		 * @param string $limit
		 */
		$ret = get_enroll_list_by_status($yue_login_id,$type,false,$limit);	
		break;
	case 'pub':		
		/**
		 * 获取我发布的活动		 
		 * @return array|int
		 */		
		$ret = get_my_event_list($yue_login_id, false, $limit);

		break;		
}


// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 5;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}


$output_arr['list'] = $ret;



$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);

?>