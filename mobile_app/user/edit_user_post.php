<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_obj = POCO::singleton ( 'pai_user_class' );
$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
$cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );

$user_id = $client_data ['data'] ['param'] ['user_id'];
$access_token = $client_data ['data'] ['param'] ['access_token'];
$json_arr = $client_data ['data'] ['param'];


$check_role = $user_obj->check_role ( $user_id );

$log_arr['result'] = $json_arr;
pai_log_class::add_log($log_arr, 'test', 'test_edit');

//$location_info = POCO::execute('common.get_location_2_location_id', $json_arr['location_id']);


foreach ($json_arr['pic_arr'] as $val)
{
	$pic_arr[] = $val['img'];
}


$update_data['nickname'] = trim($json_arr['nickname']);
$update_data['sex'] = $json_arr['sex'];
$update_data['intro'] = $json_arr['intro'];
$update_data['location_id'] = $json_arr['location_id'];
$update_data['height'] = (int)$json_arr['height'];
$update_data['weight'] = (int)$json_arr['weight'];
$update_data['cup'] = (string)$json_arr['cup'];
$update_data['chest'] = (int)$json_arr['chest'];
$update_data['chest_inch'] = (int)$json_arr['chest_inch'];
$update_data['waist'] = (int)$json_arr['waist'];
$update_data['hip'] = (int)$json_arr['hip'];
$update_data['limit_num'] = (int)$json_arr['limit_num'];
$update_data['level_require'] = (int)$json_arr['level_require'];
$update_data['pic_arr'] = $pic_arr;
$update_data['cover_img'] = $json_arr['cover_img'];
$update_data ['new_model_style_arr']= $json_arr['new_model_style_arr'];

if($check_role=='model')
{
	$ret = $model_card_obj->update_model_card($update_data, $user_id);
}
elseif($check_role=='cameraman')
{
	$ret = $cameraman_card_obj->update_cameraman_card($update_data, $user_id);
}

if($ret)
{
	$code=1;
}
else
{
	$code=0;
}

$data ['code'] = $code;

$options ['data'] = $data;

$cp->output ( $options );
?>