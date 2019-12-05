<?php 
include_once '../common.inc.php';



$alter_topic_id = (int)$_INPUT['alter_topic_id'];
$type_value = $_INPUT['type_value'];
$alter_type = $_INPUT['alter_type'];
$tag = $_INPUT['tag'];


$alter_price_log_obj = POCO::singleton('pai_alter_price_log_class');
$model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');

if(empty($alter_type))
{
	js_pop_msg("请选择改价类型");
}

$list = $alert_price_user_obj->get_user_list(false, "alter_topic_id={$alter_topic_id}", 'user_id ASC');

//更新标签
$alert_price_user_obj->update_user_topic_tag($alter_topic_id,$tag);

foreach($list as $k=>$val){
	$style_arr = $model_style_v2_obj->get_model_style_by_user_id($val['user_id']);
	foreach($style_arr as $style_val)
	{
		$insert_data['user_id'] = $val['user_id'];
		$insert_data['style'] = $style_val['style'];
		$insert_data['alter_type'] = $alter_type;
		$insert_data['type_value'] = $type_value;
		$insert_data['alter_topic_id'] = $alter_topic_id;
		
		$alter_price_log_obj->update_topic_log($insert_data);
	}
}




$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$alter_topic_id}";
js_pop_msg("修改成功",false,$url);



	


?>