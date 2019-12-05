<?php


function get_pc_model_cache($user_id)
{
	$__cache_key = "YUE_PC_MODEL_".$user_id;
	$cache_data = POCO::getCache ( $__cache_key );
	return $cache_data;
}


function set_pc_model_cache($user_id,$cache_data)
{
	$__cache_key = "YUE_PC_MODEL_".$user_id;
	$cache_time = 3600;
	POCO::setCache ( $__cache_key, $cache_data, array ('life_time' => $cache_time ) );
}

?>