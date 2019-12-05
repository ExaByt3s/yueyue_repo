<?php
/**
 * 获取分类商品列表接口
 * 2015.11.19
 * @author huanggc
 */
include_once 'config.php';

// 接收参数
$return_query = trim($_INPUT['return_query']);

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

$ret = get_api_result('customer/goods_list.php',array
	(
    'user_id' => $yue_login_id,
    'limit' => $limit,  
    'return_query' =>$return_query,
    )
);


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

// 输出数据
mall_mobile_output($output_arr,false);

?>