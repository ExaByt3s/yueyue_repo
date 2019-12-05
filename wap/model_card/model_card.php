<?php
/** 
 * 
 * 模特卡
 * 
 * author hudw
 * 
 * 
 * 2015-1-21
 * 
 * 
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/model/cache.fuc.php');


$user_id = intval($_INPUT['user_id']);



if(empty($user_id))
{
	// 这里应该有统一的返回格式
	die('数据异常');
}

$is_preview = $_INPUT['is_preview'];

$pai_obj = POCO::singleton('pai_model_card_class');

//取得模板对象
$tpl = $my_app_pai->getView('model_card_v3.html');

$tpl ->assign("rand",$random_num = date("YmdHis"));

// 公共样式和js引入
$pc_global_top = $my_app_pai->webControl('pc_global_top', array(), true);
$tpl->assign('pc_global_top', $pc_global_top);

if($is_preview)
{	
	$obj = get_pc_model_cache($user_id);

	// 实时数据
	$real_time_data = $pai_obj->get_model_card_by_user_id($user_id,9);
	
	// preview 
	foreach($obj['pic_arr'] as $key => $value)
	{
		$obj['model_pic'][$key] = array
		(
			'img'=>$value
		);
	}
	
	$obj['cup_v2'] = $obj['chest_inch'].$obj['cup'];
	$obj['user_id'] = $yue_login_id;

	$obj['take_photo_times'] = $real_time_data['take_photo_times'];
	$obj['be_follow_count'] = $real_time_data['be_follow_count'];
	$obj['level'] = $real_time_data['level']; 
	$obj['jifen'] = $real_time_data['jifen'];
	$obj['model_style_combo'] = $obj['new_model_style_arr'];
	$obj['comment_stars_list'] = $real_time_data['comment_stars_list']; 
	$obj['user_name'] = $obj['nickname'];    
	$obj['city_name'] = mb_convert_encoding($obj['city_name'],'gbk','utf-8');
	$obj['score'] = $real_time_data['score']; 


}
else
{
	
	$obj = $pai_obj->get_model_card_by_user_id($user_id,9);
	
}



$model_pic_arr_idx = 0;	
$model_pic_idx = 0;
// 整合模特卡格式
foreach ($obj['model_pic'] as $key => $value) 
{ 
  				 
	$obj['model_pic_list'][$model_pic_arr_idx]['arr'][] = array(
			'img' =>$value['img'],
			'img_idx' => $key
		);								

	$obj['model_pic_list'][$model_pic_arr_idx]['idx'] = $model_pic_arr_idx;

	$model_pic_idx++;
  	  	
 	if(($key+1) % 3 ==0 && $key == 2)
 	{
		$model_pic_arr_idx++; 
	  		    
 	}			
  		
}

//unset($obj['model_pic']);

foreach ($obj['model_type'] as $key => $value) 
{
	$obj['model_type'][$key]['text'] = $value['type'];

	unset($obj['model_type'][$key]['type']);
}

foreach ($obj['model_style'] as $key => $value) 
{
	$obj['model_style'][$key]['text'] = $value['style'];

	unset($obj['model_style'][$key]['style']);
}

foreach ($obj['model_style_v2'] as $key => $value) 
{
	$obj['model_style_v2'][$key]['text'] = $value['style'];
	$obj['model_style_v2'][$key]['combo_text'] = $value['price']."元(".$value['hour']."小时)";
	$obj['model_style_v2'][$key]['continue_text'] = "加钟续拍每小时".$value['continue_price']."元";

	unset($obj['model_style_v2'][$key]['style']);
}

$type_arr = explode(',', $obj['model_type_list']);

$obj['new_model_type_list'] = array();

foreach ($type_arr as $key => $value) 
{
	$obj['new_model_type_list'][]['text'] = $value;
	
}


// 三围
$obj['BWH'] = $obj['chest'].'-'.$obj['waist'].'-'.$obj['hip'];

// 重新整合胸围
$obj['bust'] = $obj['cup'];

// 限制人数
$obj['limit_num'] = $obj['limit_num']?$obj['limit_num'] : 0;



$obj['user_icon'] = get_user_icon($user_id,165);

/*
 * 是否已关注该用户
 * 
 * @param int    $follow_user_id    关注人用户ID
 * @param int    $be_follow_user_id 被关注人用户ID
 * 
 * return bool
 */

$pai_user_follow_obj = POCO::singleton('pai_user_follow_class');
if($yue_login_id)
{
	$is_follow = $pai_user_follow_obj->check_user_follow($yue_login_id, $user_id);
	$is_be_follow = $pai_user_follow_obj->check_user_follow($user_id, $yue_login_id);
}
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

$obj['is_follow'] = $follow_status;

$level_arr = include('/disk/data/htdocs232/poco/pai/config/level_require_config.php');
$level_key = abs($obj['level_require']-1);
$obj['level_remark'] = $level_arr[$level_key]['remark'];

// 等级
$date_obj = POCO::singleton ( 'event_date_class' );
$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$date_log = $date_obj->get_model_date_log($user_id);

foreach($date_log as $k=>$__user_id)
{
	$new_date_log[$k]['user_icon'] = get_user_icon($__user_id);
	$new_date_log[$k]['user_id'] = $__user_id;
	$new_date_log[$k]['level'] = $level;

	$level = $user_level_obj->get_user_level($__user_id);
	
	if($level==1)
	{
		$new_date_log[$k]['level1'] = 1;
		$new_date_log[$k]['level2'] = 0;
		$new_date_log[$k]['level3'] = 0;
	}
	elseif($level==2)
	{
		$new_date_log[$k]['level1'] = 0;
		$new_date_log[$k]['level2'] = 1;
		$new_date_log[$k]['level3'] = 0;
	}
	elseif($level==3)
	{
		$new_date_log[$k]['level1'] = 0;
		$new_date_log[$k]['level2'] = 0;
		$new_date_log[$k]['level3'] = 1;
	}
}




$obj['date_log'] = $new_date_log;

if($_INPUT['print'])
{
	print_r($obj);
	die();
}

$obj['user_id'] = $user_id;

$tpl ->assign("model_card_info",$obj);	


$tpl ->assign("rand",$random_num = date("YmdHis"));

$tpl->output();
?>