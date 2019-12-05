<?php
/**
 * 搜索结果
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$obj = POCO::singleton ( 'pai_search_class' );

/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;

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
					

$ret = $obj->get_search_list($nickname = mb_convert_encoding((string)$_INPUT['nickname'],'gbk','utf-8'),$b_select_count=false,$limit);	

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 9;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

if($ret){
    $output_arr['empty'] = false;
}else{
    $output_arr['empty'] = true;
}

$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '成功' : '失败';
$output_arr['list'] = $ret;


$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);


?>