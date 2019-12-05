<?php

//****************** wap版 头部通用 start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'search/index.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************

// 收集参数
$type_id = intval($_INPUT["type_id"]);


// 排序格式整合
// 初始化排序按钮
if($search_type == 'seller')
{
	$sort_btn = array(
		0 => array(
			'text' => '默认排序',
			'orderby' => '-1',
			'selected' => true
						 
		),
		1 => array(
			'text' => '销量高到低',
			'orderby' => '1'			 
		),
		2 => array(
			'text' => '评分高到低',
			'orderby' => '3'
		)
	);
}
else
{
	$sort_btn = array(
		0 => array(
			'text' => '默认排序',
			'orderby' => '-1',
			'selected' => true
					 
		),
		1 => array(
			'text' => '销量高到低',
			'orderby' => '1'	
		),
		2 => array(
			'text' => '价格高到低',	
			'orderby' => '3'
		),
		3 => array(
			'text' => '价格低到高',
			'orderby' => '4'
		),
		4 => array(
			'text' => '人气高到低',
			'orderby' => '5'
		),
		5 => array(
			'text' => '评分高到低',
			'orderby' => '7'
		)
	);
}

// 整合排序数组
if(!empty($orderby))
{
	foreach ($sort_btn as $key => $value) 
	{
		$sort_btn[$key]['selected'] = $value['orderby'] == $orderby ? true : false;
	}
}

// 输出页面数据
$output_arr['filter_data'] = $filter_data;
$output_arr['sort_data'] = $sort_btn;
$output_arr = mall_output_format_data($output_arr);

$screen_query = $_GET;
unset($screen_query['type_id']);
unset($screen_query['search_type']);
unset($screen_query['keywords']);
unset($screen_query['page']);

$_GET['screen_query'] = http_build_query($screen_query);

$page_params = mall_output_format_data($_GET);
//$keywords =  urldecode(mb_convert_encoding($keywords, 'GBK','UTF-8'));

$tpl->assign('page_params',$page_params);
$tpl->assign('page_data',$output_arr);
$tpl->assign('search_type',$search_type);
$tpl->assign('type_id',$type_id);
$tpl->assign('keywords',$keywords);

?>