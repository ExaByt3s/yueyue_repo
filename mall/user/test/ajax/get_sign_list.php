<?php
/**
 * 服务列表页
 */
 include_once 'config.php';

$goods_id = intval($_INPUT['goods_id']);
$stage_id = intval($_INPUT['stage_id']);

$stage_id = intval($_INPUT['stage_id']);

$page = intval($_INPUT['page']);


if(empty($page))
{
	$page = 1;
}


// 分页使用的page_count
$page_count = 13;

if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);
}
else
{
	$limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";

$ret = get_api_result('customer/sell_services_roster.php',array(
    'user_id' => $yue_login_id,
    'goods_id' => $goods_id,
    'stage_id' => $stage_id,
    'limit' => $limit
    ));


// print_r($ret);

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 12;

$has_next_page = (count($ret['data']['exhibit'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']['exhibit']); 
}


$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$new_array =   array();  

// $output_arr['list'] = $ret['data']['exhibit']  || $new_array;

$output_arr['list'] = $ret['data']['exhibit']  ?  $ret['data']['exhibit'] :  $new_array ;

$output_arr['title'] = $ret['data']['title'];
$output_arr['attend_str'] = $ret['data']['attend_str'];
$output_arr['attend_num'] = $ret['data']['attend_num']; 
$output_arr['total_num'] = $ret['data']['total_num']; 
$output_arr['period'] = $ret['data']['period']; 
mall_mobile_output($output_arr,false);



?>