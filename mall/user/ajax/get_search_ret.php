<?php
/**
 * 搜索列表
 * 2015.9.25
 * @author hudw <hudw@poco.cn>
 */
include_once 'config.php';

// 接收参数
$page = intval($_INPUT['page']);

$type = trim($_INPUT['search_type']);

$type_id = intval($_INPUT['type_id']) ? intval($_INPUT['type_id']) : '' ;

unset($_INPUT['_']);
unset($_INPUT['IP_ADDRESS']);
unset($_INPUT['IP_ADDRESS1']);
unset($_INPUT['request_method']);
unset($_INPUT['s']);

$filter = $_INPUT;

$filter['keyword'] = $filter['keywords'];

$filter['search_location'] = $_COOKIE['yue_location_id'] ? $_COOKIE['yue_location_id'] : 101029001;

// 处理筛选参数
$temp_screen_query_str = urldecode($filter['screen_query']);
$temp_screen_query_arr = explode("&",$temp_screen_query_str);

$screen_query_arr = array();

foreach ($temp_screen_query_arr as $key => $value) 
{
	$temp_arr = explode("=",$value);
	foreach ($temp_arr as $m_key => $m_value) 
	{
		if($m_key % 2 == 0)
		{
			$screen_query_arr[$m_value] = '';
		}
		else
		{
			$screen_query_arr[$temp_arr[$m_key-1]] = mb_convert_encoding(urldecode($m_value),'UTF-8', 'GBK');
		}
	}
	

}
$filter['screen_query'] = $screen_query_arr;

if(empty($page))
{
	$page = 1;
}

// 数据查找数
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
$filter['limit'] = $limit;
// 获取搜索结果
switch ($search_type) 
{
	case 'seller':
		$ret = get_api_result('customer/search_sellers.php',$filter);
		break;
	
	case 'goods':
		$ret = get_api_result('customer/search_services.php',$filter);
		
		break;
}



// if(empty($ret['data']))
// {
// 	/**********推荐处理**********/
// 	$mall_search_obj = POCO::singleton('pai_search_class');
// 	$loca = $_COOKIE['yue_location_id']?101029001:$_COOKIE['yue_location_id'];

// 	if($search_type == 'seller')
// 	{
// 		$recommend = $mall_search_obj->get_search_recommend_content('seller', $type_id, $loca);

// 		// 整合商家推荐数据
// 		foreach($recommend as $key => $val)
// 		{
// 			$recommend[$key]['cover'] = $val['images'];
// 			$recommend[$key]['name'] = $val['price'];
// 			$recommend[$key]['seller_bill_finish_num'] = $val['titles'];
// 			$recommend[$key]['seller_goods_num'] = $val['buy_num'];
// 			$recommend[$key]['avatar'] = get_seller_user_icon($val['seller_user_id'],165);
// 		}
// 	}
// 	else
// 	{
// 		$recommend = $mall_search_obj->get_search_recommend_content('goods', $type_id, $loca);

		
// 	}

	
	
// 	$ret['data'] = $recommend;
// 	$ret['total'] = count($recommend);
	
// 	/**********推荐处理 end **********/
// }



// 真实分页数
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