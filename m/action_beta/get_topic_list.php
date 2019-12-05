<?php

/**
 * 获取专题列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



$topic_obj = POCO::singleton('pai_topic_class');
$user_obj = POCO::singleton('pai_user_class');

$role = $user_obj->check_role($yue_login_id);



/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;

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

if($_INPUT['mode']=='action_beta')
{
	if(!$yue_login_id)
	{
		$where = "is_effect = 0";
	}
	else
	{
		$where = "(display_type='{$role}' or display_type='all') and is_effect = 0 and (type='weixin' or type='all')";
	}
}
else
{
	if(!$yue_login_id)
	{
		$where = "is_effect = 1";
	}
	else
	{
		$where = "(display_type='{$role}' or display_type='all') and is_effect = 1 and (type='weixin' or type='all')";
	}
}




$ret = $topic_obj->get_topic_list(false, $where, 'sort desc,id DESC', $limit_start);
	

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 5;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

$output_arr['list'] = $ret;


$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);

?>