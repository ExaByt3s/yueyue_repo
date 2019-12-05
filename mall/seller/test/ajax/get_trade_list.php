<?php
/**
 * 获取促销列表
 * 2015.10.13
 * @author 汤圆
 */
include_once 'config.php';

// 接收参数
$page = intval($_INPUT['page']);



if(empty($page))
{
	$page = 1;
}

// 分页使用的page_count
$page_count = 5;

if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);
}
else
{
	$limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";


$where = "status=1";

$sales_list_obj = POCO::singleton ( 'pai_topic_class' );
$ret = $sales_list_obj-> get_task_list(false, $where, 'id DESC', $limit);





// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 4;



$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

// 输出数据
mall_mobile_output($output_arr,false);

?>