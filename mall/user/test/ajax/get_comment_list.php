<?php

/**
 * 获取评价列表
 */

include_once('../common.inc.php');

$page = intval($_INPUT['page']);
$type = trim($_INPUT['type']);
$event_id = intval($_INPUT['event_id']);

$seller_id = intval($_INPUT['seller_user_id']);



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

switch ($type) 
{
	
	case 'event':
		$event_comment_obj = POCO::singleton('pai_event_comment_log_class');
		//临时
		$event_info = get_event_list ( "event_id={$event_id}" );
		$user_id = $event_info[0]['user_id'];
		$ret = $event_comment_obj->get_user_comment_list($user_id,false,$limit);
		
		if($_INPUT['print'] == 1)
		{
			print_r($event_info);
			die();
		}
		
		foreach($ret as $k=>$val){

			// 评分星星
			$has_star = intval($val['overall_score']);
			$miss_star = 5 - $has_star;

			for ($i=0; $i < 5; $i++) 
			{
				if($has_star>0)
				{
					$ret[$k]['stars_list'][$i]['is_red'] = 1; 	

					$has_star--;
				}
				else
				{
					$ret[$k]['stars_list'][$i]['is_red'] = 0; 	

					$miss_star--;						
				}
			}
		} 
		
		break;	

	case 'seller':
		$goods_id = intval($_INPUT['goods_id']);
		$buyer_id = intval($_INPUT['buyer_id']);
		
		$user_id = $buyer_id ? $buyer_id : $yue_login_id;
		
		$res = get_api_result('customer/sell_services_appraise.php',array(
			'user_id' => $user_id,
		    'seller_user_id' => $seller_id,
			'limit' => $limit,
			'goods_id' => $goods_id
		), true); 
		
		$ret = $res['data']['list'];
		
		foreach($ret as $k=>$val){

			// 评分星星
			$has_star = intval($val['rating']);
			$miss_star = 5 - $has_star;

			for ($i=0; $i < 5; $i++) 
			{
				if($has_star>0)
				{
					$ret[$k]['stars_list'][$i]['is_red'] = 1; 	

					$has_star--;
				}
				else
				{
					$ret[$k]['stars_list'][$i]['is_red'] = 0; 	

					$miss_star--;						
				}
			}
		} 
		
		
	break;
	

}



if(!$ret)
{
	$ret = array();
}


// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 8;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}



$output_arr['list'] = $ret;

$output_arr['has_next_page'] = $has_next_page;

mall_mobile_output($output_arr,false);

?>