<?php 
include_once '../../common.inc.php';

$alter_topic_id = (int)$_INPUT['alter_topic_id'];
$type = $_INPUT['type'];

$tpl = new SmartTemplate("edit_topic.tpl.htm");
$alert_price_obj = POCO::singleton('pai_alter_price_class');

if($type=='edit' && $alter_topic_id)
{
	$topic_info = $alert_price_obj->get_topic_info($alter_topic_id);
	$topic_info['begin_time'] = date("Y-m-d H:i:s",$topic_info['begin_time']);
	$topic_info['end_time'] = date("Y-m-d H:i:s",$topic_info['end_time']);
}

$tpl->assign($topic_info);
$tpl->assign("type",$type);

$tpl->output();

?>