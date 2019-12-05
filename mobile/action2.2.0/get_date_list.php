<?php

/**
 * 获取约拍邀请列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;

if(empty($page))
{
	$page = 1;
}

$status = trim($_INPUT['status']);



$user_obj = POCO::singleton ( 'pai_user_class' );

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


//加LOG  例子 http://yp.yueus.com/logs/201501/28_info.txt
$log_arr['header_list'] = headers_list();
$log_arr['user_id'] = $yue_login_id;
pai_log_class::add_log($log_arr, 'date', 'app_date');
		
		
/*
 * 模特约拍状态列表
 * @param $model_user_id 模特ID
 * @param $status 考虑中consider  已同意confirm
 * @param $b_select_count
 * @param $limit
 */

$role = $user_obj->check_role ( $yue_login_id );

if($role == 'model')
{	
	

	//$yue_login_id = 174976004;						

	$ret = get_model_status_date_list($yue_login_id,$status,false,$limit);
	
}
else
{
	//$yue_login_id = 66096046;	

	/*
	 * 摄影师约拍状态列表
	 * @param $cameraman_user_id 摄影师ID
	 * @param $status 考虑中consider  已同意confirm
	 * @param $b_select_count
	 * @param $limit
	 */
	$ret = get_cameraman_status_date_list($yue_login_id,$status,false,$limit);	
}


if(count($ret)>0)
{
	foreach ($ret as $key => $value) 
	{
		$ret[$key]['item_type'] = $status;
	}
}


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