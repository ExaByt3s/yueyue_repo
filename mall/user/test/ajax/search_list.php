<?php
/**
 * �����б�ҳ
 */
 include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$page = $_INPUT['page'];
$filter = $_INPUT['filter'];

if(empty($page))
{
	$page = 1;
}


// ��ҳʹ�õ�page_count
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

$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$ret = $mall_goods_obj -> user_search_goods_list($filter,$limit);

// ���ǰ���й������һ�����ݣ�������ʵ���
$rel_page_count = 10;

$has_next_page = (count($ret['data'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

mobile_output($output_arr,false);

?>