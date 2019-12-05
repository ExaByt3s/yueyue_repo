<?php
/**
 * 广告图片
 * zy 2014.10.9
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/*
  arr = 
  [
	{
		img : "http://image16-c.poco.cn/mypoco/myphoto/20140926/10/17497600420140926100221064.jpg?320x75_120"  //图片地址
		link_type : "inside" || "outside" || "other"// 跳转类型 内页、外页、其他
		link_address : "find",// 跳转地址
		width : 320,// 图片宽度
		height : 75 // 图片高度

	},
	{
		img : "http://image16-c.poco.cn/mypoco/myphoto/20140926/10/17497600420140926100221064.jpg?320x75_120"
		link_type : "inside" || "outside" || "other"
		link_address : "find",
		width : 320,
		height : 75 

	},
	{
		img : "http://image16-c.poco.cn/mypoco/myphoto/20140926/10/17497600420140926100221064.jpg?320x75_120"
		link_type : "inside" || "outside" || "other"
		link_address : "find",
		width : 320,
		height : 75 

	}
  ];
 */

$ads_obj = POCO::singleton('pai_ads_class');

/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']);
$position = $_INPUT['position'] ? $_INPUT['position'] : 'index';

if(empty($page))
{
	$page = 1;
}


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

$where = "position='{$position}'";
					

$ret = $ads_obj->get_ads_list(false, $where, 'sort DESC,id DESC', $limit);
	

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 5;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

$output_arr['list'] = $ret;

$output_arr['has_next_page'] = $has_next_page;

if($yue_login_id == 100001)
{
	//die('ddddddddd');
}

mobile_output($output_arr,false);



?>
