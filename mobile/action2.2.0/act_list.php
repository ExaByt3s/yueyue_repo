<?php
/**
 * hudw 2014.8.30
 * 外拍活动列表
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;
$location_id = intval($_INPUT['location_id']);
$time_querys = trim($_INPUT['time_querys']);
$price_querys = trim($_INPUT['price_querys']);
$start_querys = trim($_INPUT['start_querys']);

if(empty($page))
{
	$page = 1;
}

if(empty($time_querys))
{
	$time_querys = '';
}

if(empty($price_querys))
{
	$price_querys = '';
}

if(empty($start_querys))
{
	$start_querys = '';
}


/**
 * 活动全文高级搜索
 * 
 * @param string $time_querys  时间搜索条件 today ,weekend ,history
 * @param string $price_querys 价格搜索条件  budget_0-100, budget_100-200, budget_200-500 ,budget_500-1000
 * @param string $start_querys 发起人搜索条件 start_by_net_friends ,start_by_authority
 * @param bool $b_select_count TRUE:取记录总数 FALSE:取具体数据
 * @param string $limit 记录数
 * @return array
 */

/*
	搜索条件参数说明：
	today ：今天
	weekend:周末
	history:活动回顾

	budget_0-100：0-100
	budget_100-200：100-200
	budget_200-500：200-500
	budget_500-1000：500-1000

	start_by_net_friends：发起人 网友
	start_by_authority：发起人 官方
*/

// 分页使用的page_count
$page_count = 11;

if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}

$limit = "{$limit_start},{$page_count}";
if(in_array($yue_login_id,array(101615,100004,100106,100164,100029,100021,100001,100020,100028,100408,100410,103511)))
{
	$ret = get_event_list_no_fulltext($time_querys,$price_querys,$start_querys, $b_select_count = false, $limit,$location_id);
}
else
{
	$ret = event_fulltext_search($time_querys,$price_querys,$start_querys, $b_select_count = false, $limit,$location_id);
}
// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

if($yue_login_id == 66096046)
{
	//$output_arr['list'] = array();	
	
	//die();
}

mobile_output($output_arr,false);
?>