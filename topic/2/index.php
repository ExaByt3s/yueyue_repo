<?php
include_once("../../poco_app_common.inc.php");

$topic_obj = POCO::singleton('pai_topic_model_class');
$topic_obj->pai_settablename('pai_topic_2_tbl');
$list = $topic_obj->get_topic_list(FALSE, '', 'id ASC', '0,10');


$tpl = new SmartTemplate("index-v5.html");

//if($_GET['test'] == 1) $tpl = new SmartTemplate("index-v5.html");
$tpl->assign('list', $list);
$tpl->output();

?>