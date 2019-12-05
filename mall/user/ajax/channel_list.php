<?php
/**
 * 服务列表页
 */
 include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$type_id = intval($_INPUT['type_id']);
$query = intval($_INPUT['query']);




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

$ret = get_api_result('customer/classify_list_min.php',array(
    'query' => $query,
    'type_id' => $type_id,
	'user_id' => $yue_login_id,
	'limit' => $limit,
	));

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 5;

$has_next_page = (count($ret['data']['category_list'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']['category_list']);
}


$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret['data']['category_list'];

$output_arr['search_url'] = $ret['data']['search_url'];

mobile_output($output_arr,false);





?>