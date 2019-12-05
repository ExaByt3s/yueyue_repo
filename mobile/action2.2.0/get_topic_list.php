<?php

/**
 * 获取专题列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');


$topic_obj = POCO::singleton('pai_topic_class');
$user_obj = POCO::singleton('pai_user_class');

$role = $user_obj->check_role($yue_login_id);

/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;
$location_id = intval($_INPUT['location_id']) ;
$issue_id = intval($_INPUT['issue_id']) ;

if(empty($page))
{
	$page = 1;
}


// 分页使用的page_count
$page_count = 21;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}

$limit = "{$limit_start},{$page_count}";
 
if($issue_id)
{
	
	$cms_obj = new cms_system_class ();
	
	$ret = $cms_obj->get_record_list_by_issue_id(false, $issue_id, "0,40", "place_number ASC", $freeze=null, $where_str="");
	foreach($ret as $k=>$val)
	{
		$content_arr = explode("|",$val['content']);
		$ret[$k]['id'] = str_replace("http://yp.yueus.com/mobile/app?from_app=1#topic/","",$val['link_url']);
		$ret[$k]['cover_image'] = $content_arr[2];
	}
}
else
{
	if(!$yue_login_id)
	{
		$where = "is_effect = 1 and (type='app' or type='all')";
	}
	else
	{
		$where = "(display_type='{$role}' or display_type='all') and is_effect = 1 and (type='app' or type='all')";
	}
	
	$__is_appstore_app_ver = (preg_match('/yue_pai 1.1.10/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;
	
	if($__is_appstore_app_ver)
	{
		$where .= " and id in (99,62,65,107,121,64)";
	}

	$where .= " and version_type='old'";
	
	$ret = $topic_obj->get_topic_list(false, $where, 'sort desc,id DESC', $limit_start);
}



// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 20;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

$output_arr['list'] = $ret;

$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);

?>