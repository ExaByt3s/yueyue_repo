<?php

/**
 * 获取私人订制的地区和风格
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

include_once('/disk/data/htdocs232/poco/pai/config/model_card_config.php');

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

$obj       = POCO::singleton('pai_user_class');

if($yue_login_id>0)
{
	$user_info = $obj->get_user_info_by_user_id($yue_login_id);

	/*
	 * 检查在等待审核状态是否重复提交
	 */
	$is_duplicate  = $model_oa_order_obj ->check_duplicate_submit($user_info['cellphone']);
}

$output_arr['is_duplicate'] = $is_duplicate;

if($is_duplicate)
{
	$phone = intval($_INPUT ['phone']);

	$arr_cache = $model_oa_order_obj->get_wait_order_info_by_phone($phone);
		// 订单正在审核中
		$output_arr['code'] = 0;	
		$output_arr['data'] =  $arr_cache;
		$output_arr['msg'] = mb_convert_encoding('你的订单正在审核中...', 'gbk','utf-8');
        
		mobile_output($output_arr,false);

		exit();
}


$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');

$client_has_location = $_INPUT['client_has_location'];


$arr = array('province'=>$area_config['province'],'city'=>$area_config['city']);

$tmp_arr = array();
$model_style_arr = array();

//风格
foreach ($model_style as $key => $val) 
{
    $tmp_arr = array(
        'text' => (string)$val,
        'id' => rand(0,10000)
    );

    $model_style_arr[]=$tmp_arr;
}



if(!$client_has_location)
{
	$output_arr['two_lv_data'] = $arr;	
}

$output_arr['model_style'] = $model_style_arr;

$output_arr['model_hour'] = array(
		0=>array('id'=>2,'value'=>2,'text'=>'2小时','selected'=>true),
		1=>array('id'=>4,'value'=>4,'text'=>'4小时','selected'=>false)
	); 	

$output_arr['model_num'] = array(
		0=>array('id'=>1,'value'=>'1个','text'=>'1个','selected'=>true),
		1=>array('id'=>2,'value'=>'2个','text'=>'2个','selected'=>false),
		2=>array('id'=>3,'value'=>'3个','text'=>'3个','selected'=>false),
		3=>array('id'=>4,'value'=>'4个','text'=>'4个','selected'=>false),
		4=>array('id'=>5,'value'=>'5个','text'=>'5个','selected'=>false),
		5=>array('id'=>6,'value'=>'6个','text'=>'6个','selected'=>false),
		6=>array('id'=>7,'value'=>'7个','text'=>'7个','selected'=>false),
		7=>array('id'=>8,'value'=>'8个','text'=>'8个','selected'=>false),
		8=>array('id'=>9,'value'=>'8个以上','text'=>'8个以上','selected'=>false)
	); 			

foreach ($output_arr['model_hour'] as $key => $value) 
{
	$output_arr['model_hour'][$key]['text'] = mb_convert_encoding($output_arr['model_hour'][$key]['text'], 'gbk','utf-8');

}

foreach ($output_arr['model_num'] as $key => $value) 
{
	$output_arr['model_num'][$key]['text'] = mb_convert_encoding($output_arr['model_num'][$key]['text'], 'gbk','utf-8');
	$output_arr['model_num'][$key]['value'] = mb_convert_encoding($output_arr['model_num'][$key]['value'], 'gbk','utf-8');

}

$output_arr['code'] = 1;
$output_arr['msg'] = 'success';

mobile_output($output_arr,false);

?>