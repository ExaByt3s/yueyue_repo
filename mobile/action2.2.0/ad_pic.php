<?php
/**
 * ���ͼƬ
 * zy 2014.10.9
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/*
  arr = 
  [
	{
		img : "http://image16-c.poco.cn/mypoco/myphoto/20140926/10/17497600420140926100221064.jpg?320x75_120"  //ͼƬ��ַ
		link_type : "inside" || "outside" || "other"// ��ת���� ��ҳ����ҳ������
		link_address : "find",// ��ת��ַ
		width : 320,// ͼƬ���
		height : 75 // ͼƬ�߶�

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
 * ҳ����ղ���
 */
$page = intval($_INPUT['page']);
$position = $_INPUT['position'] ? $_INPUT['position'] : 'index';

if(empty($page))
{
	$page = 1;
}


// ��ҳʹ�õ�page_count
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
	

// ���ǰ���й������һ�����ݣ�������ʵ���
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
