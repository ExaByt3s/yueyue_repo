<?php 
include_once '../../common.inc.php';

$log_id = (int)$_INPUT['log_id'];

$alter_topic_id = (int)$_INPUT['alter_topic_id'];
$user_id = (int)$_INPUT['user_id'];
$style = $_INPUT['style'];


$tpl = new SmartTemplate("alter_price_log.tpl.htm");
$alert_price_log_obj = POCO::singleton('pai_alter_price_log_class');



if($log_id)
{
	$log_info = $alert_price_log_obj->get_log_info($log_id);
}

$tpl->assign("log_info",$log_info);
$tpl->assign("alter_topic_id",$alter_topic_id);
$tpl->assign("user_id",$user_id);
$tpl->assign("style",$style);

$tpl->output();

?>