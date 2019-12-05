<?php
/**
 * 服务列表页
 */
 include_once 'config.php';


$tab = trim($_INPUT['tab']);
$page = intval($_INPUT['page']);
$type_id = intval($_INPUT['type_id']);
$return_query = $_INPUT['return_query'];
$title = trim($_INPUT['title']);

if(empty($page))
{
	$page = 1;
}


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

$ret = get_api_result('customer/goods_list.php',array(
	'user_id' => $yue_login_id,
	'limit' => $limit,
	'return_query' =>$return_query,
	'type_id' => $type_id,
	'title'=>$title
	));


// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

$has_next_page = (count($ret['data']['goods'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']['goods']);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret['data']['goods'];

$output_arr['share'] = $ret['data']['share'];

$output_arr['title'] = $ret['data']['title'];

mall_mobile_output($output_arr,false);

?>