<?php
/**
 * 获取服务列表
 * 2015.10.12
 * @author hudw <hudw@poco.cn>
 */
include_once 'config.php';

// 接收参数
$page = intval($_INPUT['page']);
$seller_user_id = intval($_INPUT['seller_user_id']);
$img_size = trim($_INPUT['img_size']) ? trim($_INPUT['img_size']) : 'big';


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

$ret = get_api_result('customer/goods_list',array
	(
    'seller_user_id' => $seller_user_id,    
    'limit' => $limit,     
    'service_status' => $service_status,
    'goods_type'=>$goods_type
    )
);

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

$has_next_page = (count($ret['data']['list'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']['list']);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

// 输出数据
mall_mobile_output($output_arr,false);

?>