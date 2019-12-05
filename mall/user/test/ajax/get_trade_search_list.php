<?php
/**
 * 获取商家列表
 * 2015.9.25
 * @author hudw <hudw@poco.cn>
 */
include_once 'config.php';

// 接收参数
$page = intval($_INPUT['page']);

$type = trim($_INPUT['type']);

$keyword = urldecode($_INPUT['keyword']);

$type_id = intval($_INPUT['type_id']) ? intval($_INPUT['type_id']) : '' ;

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
$user_id = $yue_login_id;
switch ($type) 
{
    case 'seller':
    $ret = get_api_result('customer/search_sellers.php',array
	(
	    'user_id' => $user_id,
        'limit' => $limit,    
	    'type_id'=> $type_id,
	    'keyword' => $keyword
	    )
	);
    break;
    
    case 'goods':
     $ret = get_api_result('customer/search_services.php',array
	(
	    'user_id' => $user_id,
        'limit' => $limit,    
	    'type_id'=> $type_id,
	    'keyword' => $keyword
	    )
	);
    break;
}


// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

$has_next_page = (count($ret['data']['list'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']['list']);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret['data']['list'];

$output_arr['share'] = $ret['data']['share'];

// 输出数据
mall_mobile_output($output_arr,false);

?>