<?php 
include_once '../../common.inc.php';

$id = (int)$_INPUT['id'];

$tpl = new SmartTemplate("modify_tag.tpl.htm");

$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');

$price_user_info = $alert_price_user_obj->get_user_list(false, "id={$id}");

$tpl->assign($price_user_info[0]);

$tpl->output();

?>