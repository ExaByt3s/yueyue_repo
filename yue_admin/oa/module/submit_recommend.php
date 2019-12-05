<?php

include_once '../common.inc.php';

$oa_model_list = POCO::singleton ( 'pai_model_oa_model_list_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );

$insert_data['order_id'] = (int)$_INPUT['order_id'];
$insert_data['user_id'] = (int)$_INPUT['user_id'];
$insert_data['price'] = $_INPUT['price'];
$insert_data['type_id'] = $_INPUT['type_id'];
$insert_data['date_time'] = $_INPUT['date_time'];
$insert_data['remark'] = iconv("UTF-8", "GBK",$_INPUT['remark']);

if(empty($insert_data['order_id']) || empty($insert_data['user_id']) || empty($insert_data['price'])|| empty($insert_data['date_time']) || empty($insert_data['type_id']))
{
	exit;
}

$check_exist = $user_obj->get_user_info($insert_data['user_id']);
if(!$check_exist)
{
	echo -2;
	exit;
}

$seller_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $seller_obj->get_seller_info($insert_data['user_id'], 2);

if(!$seller_info)
{
	echo -3;
	exit;
}

$check_repeat = $oa_model_list->check_repeat($user_id,$order_id);
if($check_repeat)
{
	echo  -1;
	exit;
}

$ret = $oa_model_list->add_model($insert_data);

if($ret>1)
{
	echo 1;
}
else
{
	echo 0;
}

?>