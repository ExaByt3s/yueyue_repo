<?php
/**
 * 搜索
 */
 include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//$search_type = $_INPUT['search_type']; //用于查询翻页 page | page_list
$type_id = $_INPUT['type_id'];
$page = $_INPUT['page'];
$filter = $_INPUT;
$search_type = $_INPUT['search_type'];

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

$filter['keywords']?$filter['keywords']=iconv("UTF-8", "GB2312//IGNORE", $filter['keywords']):"";
$search_type = iconv("UTF-8", "GB2312//IGNORE", $search_type);
if($search_type == '商家')
{
	$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
	$ret = $mall_seller_obj->user_search_seller_list($filter,$limit);
}
else
{
	$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
	$ret = $mall_goods_obj -> user_search_goods_list($filter,$limit);
}


// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 9;

$has_next_page = (count($ret['data'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']);
}

$output_arr['list_data']['page'] = $page;

$output_arr['list_data']['has_next_page'] = $has_next_page;

$output_arr['list_data']['list'] = $ret;


/**********推荐处理**********/
$mall_search_obj = POCO::singleton('pai_search_class');
$loca = $_COOKIE['yue_location_id']?101029001:$_COOKIE['yue_location_id'];
if($search_type == '商家')
{
	$recommend = $mall_search_obj->get_search_recommend_content('seller', $type_id, $loca);
}
else
{
	$recommend = $mall_search_obj->get_search_recommend_content('goods', $type_id, $loca);
}
$output_arr['recommend'] = $recommend;

/**********推荐处理 end **********/

$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
$type_data = $type_obj->property_for_search_get_data($type_id);


$output_arr['filter_data']['list'] = $type_data;

mobile_output($output_arr,false);

?>