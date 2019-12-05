<?php 
include_once '../common.inc.php';


$import_url = $_INPUT['import_url'];
$import_user_id = $_INPUT['import_user_id'];
$alter_topic_id = (int)$_INPUT['alter_topic_id'];

$alert_price_topic_obj = POCO::singleton('pai_alter_price_class');
$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');
$user_obj = POCO::singleton ( 'pai_user_class' );


if(!$alter_topic_id)
{
	js_pop_msg("请先选择专题后再 导入");
	exit;
}

if($import_url)
{
	$file_user_str = file_get_contents($import_url);
	$file_user_arr = explode(",",$file_user_str);
	foreach ($file_user_arr as $val)
	{
		$__user_arr[] = $val;
	}
}

if($import_user_id)
{
	$import_user_arr = explode(",",$import_user_id);
	foreach ($import_user_arr as $val)
	{
		$__user_arr[] = $val;
	}
}

$user_arr = array_unique($__user_arr);

if(!$user_arr)
{
	js_pop_msg("导入用户为空或导入格式错误");
}

$check_repeat = $alert_price_topic_obj->check_repeat_user($alter_topic_id,$user_arr);

if($check_repeat)
{
	js_pop_msg("导入的用户在生效时间里与其他专题有重复关联");
}


foreach($user_arr as $user_id)
{
	$check_role = $user_obj->check_role ( $user_id );
	if($check_role=='model')
	{
		$insert_data['user_id'] = $user_id;
		$insert_data['alter_topic_id'] = $alter_topic_id;
		$alert_price_user_obj->add_user($insert_data);
	}
}


$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$alter_topic_id}";
js_pop_msg("导入成功",false,$url);



	


?>