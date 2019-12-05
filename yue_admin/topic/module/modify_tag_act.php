<?php 
include_once '../../common.inc.php';

$tag = $_INPUT['tag'];
$id = $_INPUT['id'];
$alter_topic_id = $_INPUT['alter_topic_id'];

$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');

$data['tag'] = $tag;
$alert_price_user_obj->update_user($data, $id);

$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$alter_topic_id}";
js_pop_msg("�޸ĳɹ�",false,$url);

?>