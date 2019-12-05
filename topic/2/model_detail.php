<?php
include_once("../../poco_app_common.inc.php");

$id = $_GET[id]?$_GET[id]:1;
if($id>10) $id=10;

$topic_obj = POCO::singleton('pai_topic_model_class');
$topic_obj->pai_settablename('pai_topic_2_tbl');

$result = $topic_obj->get_topic_info($id);

$result['z_price'] = (int)($result['price'] / 10);


$tpl = new SmartTemplate("model_detail.tpl.html");
$tpl->assign($result);
$tpl->output();
?>