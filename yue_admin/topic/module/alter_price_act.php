<?php 
include_once '../common.inc.php';


$log_id = (int)$_INPUT['log_id'];
$alter_topic_id = (int)$_INPUT['alter_topic_id'];
$type_value = $_INPUT['type_value'];
$alter_type = $_INPUT['alter_type'];
$style = $_INPUT['style'];
$user_id = (int)$_INPUT['user_id'];

$alter_price_log_obj = POCO::singleton('pai_alter_price_log_class');

if(empty($alter_type))
{
	js_pop_msg("请选择改价类型");
}

if($log_id)
{
	$data['alter_type'] = $alter_type;
	$data['type_value'] = $type_value;
	$alter_price_log_obj->update_log($data, $log_id);
}
else
{
	$insert_data['user_id'] = $user_id;
	$insert_data['style'] = $style;
	$insert_data['alter_type'] = $alter_type;
	$insert_data['type_value'] = $type_value;
	$insert_data['alter_topic_id'] = $alter_topic_id;
	
	$alter_price_log_obj->add_log($insert_data);
}

$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$alter_topic_id}";
js_pop_msg("修改成功",false,$url);



	


?>