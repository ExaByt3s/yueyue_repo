<?php
/**
 * 搜索结果
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$obj = POCO::singleton ( 'pai_search_class' );

/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']);

$location_id = intval($_COOKIE['yue_location_id']);

$tag = $_INPUT['tag'];
$price = $_INPUT['price'];
$hour = $_INPUT['hour'];
$order = $_INPUT['order'];

if($_INPUT['is_from_search']==1)
{
	$search = $tag;
	unset($tag);
}

if(empty($page))
{
	$page = 1;
}
 
// 分页使用的page_count
$page_count = 10;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}

$limit = "{$limit_start},{$page_count}";

$query['tag'] =  mb_convert_encoding(urldecode($tag),'gbk','utf-8');
$query['order'] = $order;
$query['hour'] = $hour;
$query['price'] = $price;
$query['key'] = mb_convert_encoding(urldecode($search),'gbk','utf-8');

$ret = $obj->get_search_combo_list($query,$location_id,$limit);

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 9;

$has_next_page = (count($ret['list'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['list']);
}

if(count($ret['list']) == 0 && $page == 1){
    $output_arr['empty'] = true;
}else{
    $output_arr['empty'] = false;
}

$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '成功' : '失败';
$output_arr['list'] = $ret;


$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);


?>