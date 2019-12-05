<?php
/**
 * 获得搜索标签
 */
 include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$activity_code_obj = POCO::singleton('pai_activity_code_class');
$page = $_INPUT['page'];


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

$ret = get_api_result('customer/get_qrcode_list.php',array(
	'user_id' => $yue_login_id,
	'limit' => $limit
	));


// print_r($ret);

foreach ($ret['data']['list'] as $key => $value) {
	foreach ($value['tickets'] as $k => $v) {
		$ret['data']['list'][$key]['tickets'][$k]['qrcode'] = $activity_code_obj->get_qrcode_img($v['qrcode']);
	}
}

// print_r($ret);
// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 5;

$has_next_page = (count($ret['data']['list'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']['list']);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

mobile_output($output_arr,false);

?>