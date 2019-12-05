<?php
include('no_copy_online_config.inc.php');

// 项目路径，使用的是绝对路径 
$base_url = G_MALL_PROJECT_USER_ROOT;


$app_to_webpage_config = array(
    '1220094' => $base_url.'/goods/category.php', // 分类首页
	'1220130' => $base_url.'/topic/index.php', // 专题详情页
	'1220109' => $base_url.'/seller/service_list.php?tag=seller', //商家服务列表
	'1220111' => $base_url.'/seller/detail.php',// 商家详情介绍页
	'1220103' => $base_url.'/seller/index.php',// 商家主页 
	'1220147' => $base_url.'/seller/seller_list.php',// 商家列表 大图
	'1220102' => $base_url.'/goods/service_detail.php', // 服务详情


	'1220101' => $base_url.'/goods/service_list.php?tag=hp',//横排服务列表
	'1220122' => $base_url.'/goods/service_list.php?tag=dt', // 大图服务列表
	'1220128' => $base_url.'/goods/service_list.php?tag=lp', // 两排服务列表
	
	'1220152' => $base_url.'/act/detail.php',// 活动详情
	'1220130' => $base_url.'/topic/index.php',// 专题详情
	'0000001' => $base_url.'/act/list.php',// 外拍列表 特殊处理！！！！
	'1220144' => $base_url.'/category/index.php',// 品类首页
	'1220146' => $base_url.'/seller/seller_list.php?img_size=small',// 商家列表 小图
	'1220145' => $base_url.'/channel/index.php',// 子频道列表
	'1220075' => $base_url.'/seller/comment_list.php',// 评价列表页
	'1220098' => $base_url.'/search/search.php?type_id=99',// 搜索页,
	'1220124' => $base_url.'/search/search.php',// 搜索页
	'1220125' => $base_url.'/search/index.php?search_type=seller',// 商家搜索页
	'1220126' => $base_url.'/search/index.php?search_type=goods'// 服务搜索页
);


if($user_agent_arr['is_pc'] == 1 )
{	// 上线删掉
	$app_to_webpage_config['1220124'] = $base_url.'/search/index.php?search_type=goods';// 服务搜索页
}


return $app_to_webpage_config;
?>