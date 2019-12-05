<?php
/**
 * 报名服务列表
 * @author hudw 
 * 2015.10.13
 */

include_once 'config.php';

// 接收参数
$page = intval($_INPUT['page']);
$topic_id = intval($_INPUT['topic_id']);
$ret = array();

if(empty($page))
{
	$page = 1;
}

// 分页使用的page_count
$page_count = 11;

if($yue_login_id == 100004)
{
	$page_count = 5;
}

if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);
}
else
{
	$limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";

$mall_topic_obj = POCO::singleton('pai_topic_class');

$ret = $mall_topic_obj->get_enroll_service_list($topic_id,$yue_login_id,$limit);

$index = ($page - 1)*($page_count - 1);

foreach($ret as $k=>$val)
{

    $ret[$k]['arr_index'] = $index;
    $index++;
}


if(count($ret) == 0)
{
	$ret = array();
}

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

if($yue_login_id == 100004)
{
	$rel_page_count = 4;
}

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