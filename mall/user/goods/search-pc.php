<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods/search.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部bar
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');
// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 下载区域
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/down-app-area.php');

$pc_global_top = _get_wbc_head();
$global_top_bar = _get_wbc_global_top_bar();
$footer = _get_wbc_footer();
$down_app_area = _get_wbc_down_app_area();


$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc版头部通用 end ==================

if(empty($page))
{
	$page = 1;
}

if(empty($orderby))
{
	$orderby = '-1';
}

// ================== 收集参数 ====================
$filter = $_INPUT;
$filter['keywords'] = $keywords;
$is_no_data = false;
$is_seller = ($search_type=='seller');
// 分页使用的page_count
$page_count = 9;

if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);
}
else
{
	$limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";



/**********分页处理**********/

if($search_type == 'seller')
{
	$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
	$ret = $mall_seller_obj->user_search_seller_list($filter,$limit);

	// 重新整合列表数据
	$ret = $mall_seller_obj->seller_data_for_front_packing($ret);
}
else
{
	$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
	$ret = $mall_goods_obj -> user_search_goods_list($filter,$limit);

	// 重新整合列表数据
	$ret = $mall_goods_obj->goods_data_for_front_packing($ret);
	
}



// 用于显示没有数据的文案
if($ret['total'] == 0)
{
	$is_no_data = true;
}

if(empty($ret['data']))
{
	/**********推荐处理**********/
	$mall_search_obj = POCO::singleton('pai_search_class');
	$loca = $_COOKIE['yue_location_id']?101029001:$_COOKIE['yue_location_id'];

	if($search_type == 'seller')
	{
		$recommend = $mall_search_obj->get_search_recommend_content('seller', $type_id, $loca);

		// 整合商家推荐数据
		foreach($recommend as $key => $val)
		{
			$recommend[$key]['cover'] = $val['images'];
			$recommend[$key]['name'] = $val['price'];
			$recommend[$key]['seller_bill_finish_num'] = $val['titles'];
			$recommend[$key]['seller_goods_num'] = $val['buy_num'];
			$recommend[$key]['avatar'] = get_seller_user_icon($val['seller_user_id'],165);
		}
	}
	else
	{
		$recommend = $mall_search_obj->get_search_recommend_content('goods', $type_id, $loca);

		
	}

	
	
	$ret['data'] = $recommend;
	$ret['total'] = count($recommend);
	
	/**********推荐处理 end **********/
}

unset($_GET['p']);

$page_obj = new show_page ();
$page_obj->file = "search.php?";
$total_count = $ret['total'];
$show_count = 9 ;
$page_obj->setvar ($_GET);
$page_obj->sethash ('dw');
$page_obj->set ( $show_count, $total_count );

if ($show_count > $total_count) 
{
    $page_show = '';
}
else
{
    $page_show = $page_obj->output ( 1 ) ;
}
$tpl->assign ( "page", $page_show );
/**********分页处理 end **********/


$list_data['page'] = $page;

$list_data['has_next_page'] = $has_next_page;

$list_data['list'] = $ret;

$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$mall_goods_obj->hotgoods_list(array('type_id'=>$type_id),'0,9');

$key_current = $MALL_COLUMN_CONFIG[$type_id] ;

// 没有类型匹配的时候
if(!$key_current)
{
	// 关键词配置
	$title_key = '约约--最高效的时间电商平台';
	$keywords_key ='约约，模特，摄影师，约拍，约摄，服务，时间电商，技能服务';
	$description_key ='约约是最高效的时间电商O2O平台，通过时间电商平台，每个用户都能利用自己的零碎时间，提供摄影、技能等相关的服务来创造价值。';

	$key_current['title_key'] = $title_key;
	$key_current['keywords_key'] = $keywords_key;
	$key_current['description_key'] = $description_key;
}

$tpl->assign('key_current', $key_current);
$tpl->assign('list_data',$list_data);

if ($_INPUT['print']) 
{
    print_r($ret['data']);
}

// 获取所有类型 模特、培训、摄影.....
$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_data = $type_obj->get_type_cate(2);

array_unshift($type_data, array('name'=>'不限','type_id'=>$type_id));

// 热门搜索文案整合
$mall_search_obj = POCO::singleton('pai_search_class');
$location_id = $_COOKIE['yue_location_id']?101029001:$_COOKIE['yue_location_id'];
if($search_type == 'seller')
{
	$get_hot_search_tag = $mall_search_obj->get_search_tag('seller', $type_id, $location_id);
}
else
{
	$get_hot_search_tag = $mall_search_obj->get_search_tag('goods', $type_id, $location_id);
}

if(!is_array($get_hot_search_tag))
{
	$get_hot_search_tag = '';
}
else
{	
	// 存在热门推荐时处理
	foreach($get_hot_search_tag as $key => $val)
	{		
		// 重新整合url参数
		$url = mall_yueyue_app_to_http($val['link']);

		$url_arr = parse_url($url);

		parse_str($url_arr['query'], $parse_arr);			

		$get_hot_search_tag[$key]['link'] = $url_arr['scheme'].'://'.$url_arr['host'].'/'.$url_arr['path'].'?'.$parse_arr['return_query'];
	}

}

// 筛选 类型整合
$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');

$tpl->assign('filter_data',mall_output_format_data($filter_data));

$querys = '';
$temp_query = array(
	'keywords' => $keywords,
	'search_type' => $search_type	
);
$querys = http_build_query($temp_query);
$url = './search.php?'.$querys;

// 重新整合类型数据
foreach($type_data as $key => $val)
{
	$type_data[$key]['link'] = $url.'&type_id='.$val['id'];

	if($val['id'] == $type_id)
	{
		$type_data[$key]['selected'] = true;		 
	}
}

// 重新整合筛选数据
if(!$is_seller)
{
	$filter_data = $type_obj->property_for_search_get_data($type_id);

	foreach($filter_data as $key => $val)
	{
		$detial_name = $val['name'];
		
		if(!empty($val['data']))
		{
			$child_filter_a = $val['data'];

			foreach($child_filter_a as $i_key => $i_val)
			{			

				$temp_a_query = array(
					$detial_name => $i_val['key'],
					'a_key' => $i_val['key'],
					'type_id' => $type_id				
				);
	 
				// 转换第一级筛选数据				
				$temp_a_querys = http_build_query($temp_a_query);

				// 设置跳转链接
				$filter_data[$key]['data'][$i_key]['link'] = $url.'&'.$temp_a_querys;
				// 设置高亮
				$filter_data[$key]['data'][$i_key]['selected'] = ($a_key == $i_val['key'])?true:false;

				if(!empty($i_val['child_data']['data']))
				{
					$child_filter_b = $i_val['child_data']['data'];				

					foreach($child_filter_b as $j_key => $j_val)
					{
						$temp_b_query = array
						(
							$i_val['child_data']['name'] => $j_val['key'],
							'b_key' =>$j_val['key'],
							'type_id' => $type_id
						);

						// 转换第二级筛选数据				
						$temp_b_querys = http_build_query($temp_b_query);	

						// 设置跳转链接
						$filter_data[$key]['data'][$i_key]['child_data']['data'][$j_key]['link'] = $url.'&'.$temp_a_querys.'&'.$temp_b_querys;
						// 设置高亮
						$filter_data[$key]['data'][$i_key]['child_data']['data'][$j_key]['selected'] = ($b_key == $j_val['key'])?true:false;								
					}

					// 如果存在第一级筛选，就将第二级的筛选数据格式加在第一级里面
					if($a_key == $i_val['key'])
					{
						$child_filter_b_temp = $filter_data[$key]['data'][$i_key]['child_data'];
						$child_filter_b_temp['text'] = $i_val['val'];
					}
				}
			}
		}
	}
}


if($a_key && !empty($child_filter_b_temp))
{
	$temp_arr_a = array();

	array_splice($filter_data,1,0,array($child_filter_b_temp));
}

// 初始化排序按钮
if($search_type == 'seller')
{
	$sort_btn = array(
		0 => array(
			'text' => '综合排序',
			'sort' => '-1,-1',
			'orderby' => '',
						 
		),
		1 => array(
			'text' => '按销售量',
			'sort' => '1,1',
			'orderby' => ''			 
		),
		2 => array(
			'text' => '按评分',
			'sort' => '3,3',
			'orderby' => ''
		)
	);
}
else
{
	$sort_btn = array(
		0 => array(
			'text' => '综合排序',
			'sort' => '-1,-1',
			'orderby' => '',
					 
		),
		1 => array(
			'text' => '按销售量',
			'sort' => '1,1',
			'orderby' => ''	
		),
		2 => array(
			'text' => '按价格',
			'sort' => '4,3',
			'id'   => 'price',	
			'orderby' => ''
		),
		3 => array(
			'text' => '按人气',
			'sort' => '5,5',
			'orderby' => ''
		),
		4 => array(
			'text' => '按评分',
			'sort' => '7,7',
			'orderby' => ''
		)
	);
}

// 整合排序数组
if(!empty($orderby))
{
	foreach($sort_btn as $key => $val)
	{
		$temp_sort_arr = explode(",", $val['sort']);
		// 只有id这个字段才能有升降序玩，否则的都是默认降序
		if(isset($val['id']))
		{
			
			foreach($temp_sort_arr as $m_key => $m_val)
			{
				if($m_val == $orderby)
				{
					

					$sort_btn[$key]['orderby'] = $orderby;

					$arrow = '';

					// 价格默认是升序					
					if(isset($val['id']) && isset($val['id']) == 'price')
					{
						if($m_key % 2 == 0)
						{
							$arrow = 'asc';
						}
						else
						{
							$arrow = 'desc';	
						}				
					}		

					$sort_btn[$key]['selected'] = $orderby == $m_val['orderby'] ? true : false;
					$sort_btn[$key]['arrow'] = $arrow;

					break;
				}
				
			}
		}
		else
		{
			$sort_btn[$key]['selected'] = $orderby == $temp_sort_arr[1] ? true : false;
			$sort_btn[$key]['orderby'] = $temp_sort_arr[0];
			$sort_btn[$key]['arrow'] = 'fn-hide';
		}

		
	}
}



// 输出页面数据
$output_arr['type_data'] = $type_data;
$output_arr['filter_data'] = $filter_data;
$output_arr['input_data'] = array
(
	'text' => $keywords,
	'place_holder' => '请输入关键字',
	'hot_data' => $get_hot_search_tag,
	'type_id' => $type_id,
	'search_type' => $search_type,
	'is_no_data' => $is_no_data

);
$output_arr['sort_data'] = array
(
	'keywords' =>$keywords,
	'total' => $total_count,
	'sort_btn' => $sort_btn,
	'orderby' => $orderby,
	'is_no_data' => $is_no_data
);



$tpl->assign('is_seller',$is_seller);
$tpl->assign('page_data',mall_output_format_data($output_arr));



?>