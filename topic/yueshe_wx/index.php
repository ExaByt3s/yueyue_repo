<?php


include_once('show.php');

exit;
$tpl = $my_app_pai->getView('index.tpl.htm');

$header_html = $my_app_pai->webControl('yueshe_wx_topic_header', array(), true);
$tpl->assign("header_html",$header_html);

$footer_html = $my_app_pai->webControl('yueshe_wx_topic_footer', array("id"=>$id), true);
$tpl->assign("footer_html",$footer_html);


$tpl->assign("id",$id);

$obj = new cms_system_class ();

$record_list = $obj->get_record_list_by_issue_id(false, $id, "0,1", "place_number ASC", $freeze=null, $where_str="");


$tpl->assign($record_list[0]);

$tpl->output();
 ?>