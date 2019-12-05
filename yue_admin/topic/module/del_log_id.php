<?php 
include_once '../common.inc.php';


$log_id = (int)$_INPUT['log_id'];
$alter_topic_id = (int)$_INPUT['alter_topic_id'];

$alter_price_log_obj = POCO::singleton('pai_alter_price_log_class');

$alter_price_log_obj->del_log($log_id);

$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$alter_topic_id}";
js_pop_msg("Çå¿Õ³É¹¦",false,$url);

?>