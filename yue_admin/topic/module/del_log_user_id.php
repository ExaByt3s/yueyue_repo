<?php 
include_once '../common.inc.php';


$user_id = (int)$_INPUT['user_id'];
$alter_topic_id = (int)$_INPUT['alter_topic_id'];

$alter_price_log_obj = POCO::singleton('pai_alter_price_log_class');
$alter_price_user_obj = POCO::singleton('pai_alter_price_user_class');

$alter_price_log_obj->del_log_by_user_id($alter_topic_id,$user_id);
$alter_price_user_obj->del_user_by_user_id($alter_topic_id,$user_id);

$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$alter_topic_id}";
js_pop_msg("Çå¿Õ³É¹¦",false,$url);

?>