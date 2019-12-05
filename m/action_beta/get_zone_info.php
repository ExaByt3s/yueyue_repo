<?php

/**
 * 获取用户空间信息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$role = trim($_INPUT['role']);

$user_id = intval($_INPUT['user_id']);

if($role == 'model')
{
	$pai_obj = POCO::singleton('pai_model_card_class');
	/*
	 * 根据用户ID获取模特卡数据
	 * @param int $user_id
	 * return array
	 */

	$ret = $pai_obj ->get_model_card_by_user_id($user_id,6);	 	

	$obj = $ret;

	foreach ($obj['model_pic'] as $key => $value) 
	{    
	 
		if($key == 3)
		{
			$obj['model_pic'][$key]['type'] = 'double';		
		}
		else if($key == 4 || ($key+1)%3==0)
		{
			$obj['model_pic'][$key]['type'] = 'special';	
		}
		else
		{
			$obj['model_pic'][$key]['type'] = 'one';			
		}

		$obj['model_pic'][$key]['user_icon'] = $value['img'];			

		unset($obj['model_pic'][$key]['img']);
	}

	/*foreach ($obj['model_type'] as $key => $value) 
	{
		$obj['model_type'][$key]['text'] = $value['type'];

		unset($obj['model_type'][$key]['type']);
	}*/

	foreach ($obj['model_style'] as $key => $value) 
	{
		$obj['model_style'][$key]['text'] = $value['style'];

		unset($obj['model_style'][$key]['style']);
	}


	// 三围
	$obj['BWH'] = $obj['chest'].'-'.$obj['waist'].'-'.$obj['hip'];

	// 重新整合胸围
	$obj['bust'] = $obj['chest'].''.$obj['cup'];


	$poco_obj =  POCO::singleton('pai_user_class');


}
else
{

	$poco_obj       = POCO::singleton('pai_user_class');

	$ret = $poco_obj->get_user_info_by_user_id($user_id);

	$obj = $ret;

	$obj['user_icon'] = get_user_icon($user_id,165).'?'.time();
	

	//$paij_obj = POCO::singleton('pai_cameraman_card_class');
	/*
	* 根据用户ID获取摄影师卡数据
	* @param int $user_id
	* return array
	*/
	/*$ret = $paij_obj->get_cameraman_card_by_user_id($user_id);

	$obj = $ret[0];

	// 模特卡
	foreach ($obj['cameraman_pic'] as $key => $value) 
	{    

		if(($key+1)%3==0)
	{
		$obj['model_pic'][$key]['type'] = 'special';			
	}
	else
	{
		$obj['model_pic'][$key]['type'] = 'one';	
	}

	$obj['model_pic'][$key]['user_icon'] = $value['img'];			

	}

	// 评分星星
	$has_star = (int)intval($obj['attendance'])/2;
	$miss_star = 5 - $has_star;

	for ($i=0; $i < 5; $i++) 
	{
	if($has_star>0)
	{
		$obj['attendance_list'][$i]['is_red'] = 1; 	

		$has_star--;
	}
	else
	{
		$obj['attendance_list'][$i]['is_red'] = 0; 	

		$miss_star--;						
	}

	}

	// 出勤星星

	$has_star = (int)intval($obj['comment_level'])/2;
	$miss_star = 5 - $has_star;

	for ($i=0; $i < 5; $i++) 
	{
	if($has_star>0)
	{
		$obj['comment_level_list'][$i]['is_red'] = 1; 	

		$has_star--;
	}
	else
	{
		$obj['acomment_level_list'][$i]['is_red'] = 0; 	

		$miss_star--;						
	}

	}*/
}

$output_arr['data'] = $obj;

if($user_id == 100001)
{
	//sleep(5); 
}

mobile_output($output_arr,false);

?>