<?php
include_once("../../poco_app_common.inc.php");

$topic_obj = POCO::singleton('pai_topic_model_class');
$list = $topic_obj->get_topic_list(FALSE, '', 'id ASC', '0,10');

$tpl = new SmartTemplate("index_v3.html");
if($_GET['tpl']=='index_v3') $tpl = new SmartTemplate("index_v3.html");
$tpl->assign('list', $list);
$tpl->output();

?>