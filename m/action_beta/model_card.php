<?php

/**
 * hudw 2014.8.21
 * ģ�ؿ���ҳ
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$user_id = $_INPUT['user_id'];

$pai_obj = POCO::singleton('pai_model_card_class');

$ret = $pai_obj->get_model_card_by_user_id($user_id,15);

//$style['style']='��˽�ĵ�һ����';
//$style['price']= 50;
//$style['params']= 'discount';
//$style['discount']= '1�ۼ�50Ԫ';
//$style['is_discount']= 1;

//�������Ķ�

//array_unshift($ret['model_style'],$style);


$obj = $ret;

$model_pic_arr_idx = 0;
$model_pic_idx = 0;

foreach ($obj['model_pic'] as $key => $value) 
{ 
  	
	
  
	if($model_pic_idx == 0)
	{
		$obj['model_pic'][$key]['type'] = 'double';		
	}
	else
	{
		$obj['model_pic'][$key]['type'] = 'one';			
	}

	$obj['model_pic'][$key]['user_icon'] = $value['img'];

	$obj['new_model_pic'][$model_pic_arr_idx][] = array
	(
		'type'=>$obj['model_pic'][$key]['type'],
		'user_icon'=>yueyue_resize_act_img_url($value['img'],'260') ,
		'big_user_icon' => yueyue_resize_act_img_url($value['img'],'640') 
	);	
  
  	$model_pic_idx++;

 	if( $key == 2 || $key == 8 || $key == 14)
 	{
		$model_pic_arr_idx++; 
	  
 	}			
  
	unset($obj['model_pic'][$key]['img']);


}

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
	$obj['model_style_v2'][$key]['combo_text'] = $value['price']."Ԫ(".$value['hour']."Сʱ)";
	$obj['model_style_v2'][$key]['continue_text'] = "��������ÿСʱ".$value['continue_price']."Ԫ";

	unset($obj['model_style_v2'][$key]['style']);
}

$type_arr = explode(',', $obj['model_type_list']);

$obj['new_model_type_list'] = array();

foreach ($type_arr as $key => $value) 
{
	$obj['new_model_type_list'][]['text'] = $value;
	
}


// ��Χ
$obj['BWH'] = $obj['chest'].'-'.$obj['waist'].'-'.$obj['hip'];

// ����������Χ
$obj['bust'] = $obj['cup'];

// ��������
$obj['limit_num'] = $obj['limit_num']?$obj['limit_num'] : 0;



$obj['user_icon'] = get_user_icon($user_id,165);

/*
 * �Ƿ��ѹ�ע���û�
 * 
 * @param int    $follow_user_id    ��ע���û�ID
 * @param int    $be_follow_user_id ����ע���û�ID
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


$date_obj = POCO::singleton ( 'event_date_class' );
$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$date_log = $date_obj->get_model_date_log($user_id);

foreach($date_log as $k=>$__user_id)
{
	$new_date_log[$k]['user_icon'] = get_user_icon($__user_id);
	$new_date_log[$k]['user_id'] = $__user_id;
	
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

// modify by hudw
// 2015.2.18
$obj['intro'] =  str_replace("&lt;br&gt;",'', $obj['intro']);

$obj['date_log'] = $new_date_log;

$output_arr['data'] = $obj;


mobile_output($output_arr,false);

?>