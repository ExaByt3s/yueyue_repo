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


}
else
{

	$poco_obj       = POCO::singleton('pai_user_class');

	$ret = $poco_obj->get_user_info_by_user_id($user_id);

	unset($ret['cellphone'],$ret['phone'],$ret['app_access_token'],$ret['available_balance'],$ret['bail_available_balance'],$ret['bail_available_balance'],$ret['balance'],$ret['payable']);

	$obj = $ret;

	$obj['user_icon'] = get_user_icon($user_id,165).'?'.time();

	$date_obj = POCO::singleton ( 'event_date_class' );
	$score_obj = POCO::singleton ( 'pai_score_rank_class' );
	$date_rank_obj = POCO::singleton ( 'pai_date_rank_class' );

	$date_log = $date_obj->get_cameraman_date_log($user_id);

	foreach($date_log as $k=>$__user_id)
	{
		$new_date_log[$k]['user_icon'] = get_user_icon($__user_id);
		$new_date_log[$k]['user_id'] = $__user_id;
		
		$score_arr = $score_obj->get_score_rank($__user_id);

		$new_date_log[$k]['score'] = $score_arr['score'];
	}

	$obj['date_log'] = $new_date_log;
	
	$obj['take_photo_times'] = $date_rank_obj->count_cameraman_take_photo_times ( $user_id );;
	
	$obj['remark'] = '';

	$obj['style_like'] = '';

	/*
	 * 是否已关注该用户
	 * 
	 * @param int    $follow_user_id    关注人用户ID
	 * @param int    $be_follow_user_id 被关注人用户ID
	 * 
	 * return bool
	 */

	if($yue_login_id)
	{
		$pai_user_follow_obj = POCO::singleton('pai_user_follow_class');

		$is_follow = $pai_user_follow_obj->check_user_follow($yue_login_id, $user_id);
		$is_be_follow = $pai_user_follow_obj->check_user_follow($user_id, $yue_login_id);

		if($is_follow && $is_be_follow)
		{
			$follow_status=2;
		}
		elseif($is_follow)
		{
			$follow_status = 1;
		}
		else
		{
			$follow_status = 0;
		}
	}

	$obj['is_follow'] = $follow_status;


	/********************重新整合模特卡***********************/ 
	$model_pic_arr_idx = 0;
	$model_pic_idx = 0;

	foreach ($obj['pic_arr'] as $key => $value) 
	{ 
	  	
			  
		if($model_pic_idx == 0)
		{
			$obj['pic_arr'][$key]['type'] = 'double';		
		}
		else
		{
			$obj['pic_arr'][$key]['type'] = 'one';			
		}

		$obj['pic_arr'][$key]['user_icon'] = $value['img'];

		$obj['new_pic_arr'][$model_pic_arr_idx][] = array
		(
			'type'=>$obj['pic_arr'][$key]['type'],
			'user_icon'=>$value['img'],
			'big_user_icon' => str_replace("_260.",".",$value['img'])
		);	
	  
	  	$model_pic_idx++;

	 	if( $key == 2 || $key == 8 || $key == 14)
	 	{
			$model_pic_arr_idx++; 		  
	 	}			
	  


	}
	


}

$output_arr['data'] = $obj;


mobile_output($output_arr,false);

?>